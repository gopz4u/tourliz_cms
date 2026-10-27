<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\CustomItinerary;
use App\Models\Destination;
use App\Helpers\ItineraryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class B2BItineraryController extends Controller
{
    /**
     * Display a listing of B2B itineraries.
     */
    public function index(Request $request)
    {
        $applyFilters = function ($query, bool $includeAgency = true, bool $includeFollowup = true) use ($request) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('client_name', 'like', "%{$search}%")
                        ->orWhere('id', 'like', '%' . str_replace('QT-', '', $search) . '%');
                });
            }

            if ($request->filled('destination_id')) {
                $query->where('destination_id', $request->destination_id);
            }
            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }
            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }
            if ($request->filled('status')) {
                $query->where('payment_status', $request->status);
            }
            if ($includeAgency && $request->filled('agency_id')) {
                $query->where('agency_id', $request->agency_id);
            }
            if ($includeFollowup && $request->filled('followup')) {
                $query->where('followup_status', $request->followup);
            }
            if ($request->filled('date_status')) {
                if ($request->date_status === 'unscheduled') {
                    $query->whereIn('travel_date_status', ['tbd', 'flexible']);
                } else {
                    $query->where('travel_date_status', $request->date_status);
                }
            }

            return $query;
        };

        $listQuery = $applyFilters(CustomItinerary::query());
        $itineraries = $listQuery
            ->with(['agency', 'destination', 'expenses', 'user', 'supplier'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Conversion metrics deliberately ignore the follow-up filter so selecting
        // "Converted" does not make every displayed conversion rate become 100%.
        $metricQuery = $applyFilters(CustomItinerary::query(), true, false);
        $totalItineraries = (clone $metricQuery)->count();
        $convertedItineraries = (clone $metricQuery)->where('followup_status', 'converted')->count();
        $conversionRate = $totalItineraries > 0
            ? round(($convertedItineraries / $totalItineraries) * 100, 1)
            : 0;
        $activeAgencies = (clone $metricQuery)->distinct()->count('agency_id');

        // The comparison always shows every agency, even when one agency is selected
        // in the itinerary list filter.
        $agencyStats = $applyFilters(CustomItinerary::query(), false, false)
            ->selectRaw("agency_id, COUNT(*) as total_itineraries, SUM(CASE WHEN followup_status = 'converted' THEN 1 ELSE 0 END) as converted_itineraries")
            ->with('agency:id,company_name')
            ->groupBy('agency_id')
            ->orderByDesc('total_itineraries')
            ->get()
            ->each(function ($stat) {
                $stat->conversion_rate = $stat->total_itineraries > 0
                    ? round(($stat->converted_itineraries / $stat->total_itineraries) * 100, 1)
                    : 0;
            });

        $destinations = \App\Models\Country::orderBy('name')->get();
        $agencies = Agency::orderBy('company_name')->get(['id', 'company_name']);

        return view('admin.b2b.index', compact(
            'itineraries',
            'destinations',
            'agencies',
            'agencyStats',
            'totalItineraries',
            'convertedItineraries',
            'conversionRate',
            'activeAgencies'
        ));
    }

    /**
     * Show the form for creating a new itinerary.
     */
    public function create(Request $request)
    {
        $agencies = Agency::orderBy('company_name')->get();
        $destinations = \App\Models\Country::orderBy('name')->get();
        $admins = \App\Models\Admin::orderBy('name')->get();
        $selected_agency_id = $request->get('agency_id');
        return view('admin.b2b.create', compact('agencies', 'destinations', 'admins', 'selected_agency_id'));
    }

    /**
     * Store a newly created itinerary.
     */
    public function store(Request $request)
    {
        $this->prepareTravelDateRequest($request);

        $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'destination_id' => 'required|exists:countries,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'title' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'travel_date_status' => 'required|in:tbd,flexible,exact',
            'start_date' => ['nullable', 'date', Rule::requiredIf($request->travel_date_status === 'exact')],
            'preferred_travel_month' => 'nullable|date_format:Y-m',
            'travel_date_notes' => 'nullable|string|max:255',
        ]);

        // Generate blank/sample itinerary structure
        $itineraryStructure = ItineraryHelper::generateSampleItinerary($request->duration_days, $request->destination_id);

        // Use a simpler approach: Initialize empty structure
        foreach ($itineraryStructure as &$day) {
            $day['places'] = [];
            // Keep structure clean
        }

        $itinerary = CustomItinerary::create(array_merge([
            'user_id' => $request->user_id ?? auth()->id(),
            'agency_id' => $request->agency_id,
            'destination_id' => $request->destination_id,
            'country_ids' => $request->country_ids ?? [],
            'title' => $request->title,
            'client_name' => $request->client_name,
            'duration_days' => $request->duration_days,
            'itinerary' => $itineraryStructure,
            'status' => 'draft',
            'markup_percentage' => 10.00, // Default markup
        ], $this->travelDateData($request)));

        $itinerary->calculatePricing();
        $itinerary->save();

        return redirect()->route('admin.b2b-itineraries.edit', $itinerary->id)
            ->with('success', 'Draft itinerary created. Now add details.');
    }

    /**
     * Show the builder to edit the itinerary.
     */
    public function edit($id)
    {
        $itinerary = CustomItinerary::with(['agency', 'destination'])->findOrFail($id);
        $involvedVendorIds = \App\Models\ItineraryExpense::where('itinerary_id', $itinerary->id)
            ->where('itinerary_type', 'b2b')
            ->whereNotNull('supplier_id')
            ->pluck('supplier_id')->map(fn ($vendorId) => (int) $vendorId)->unique()->values();
        $countries = \App\Models\Country::orderBy('name')->get();
        // Here destinations refers to cities in our destinations table
        $destinations = \App\Models\Destination::orderBy('city')->get();
        $admins = \App\Models\Admin::orderBy('name')->get();

        return view('admin.b2b.edit', compact('itinerary', 'countries', 'destinations', 'admins', 'involvedVendorIds'));
    }

    /**
     * Update the itinerary details from the builder.
     */
    public function update(Request $request, $id)
    {
        $itinerary = CustomItinerary::findOrFail($id);
        $requiresExactDate = in_array($request->input('followup_status', $itinerary->followup_status), ['converted'], true)
            || $request->input('status', $itinerary->status) === 'confirmed';
        $this->prepareTravelDateRequest($request, $requiresExactDate, $itinerary);

        $request->validate([
            'itinerary' => 'required|json',
            'markup_percentage' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'client_name' => 'nullable|string',
            'title' => 'required|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'adults' => 'required|integer|min:1',
            'children_2_6' => 'nullable|integer|min:0',
            'children_6_11' => 'nullable|integer|min:0',
            'payment_status' => 'nullable|string',
            'total_amount_received' => 'nullable|numeric|min:0',
            'payment_details' => 'nullable|string',
            'followup_status' => 'nullable|string',
            'next_followup_date' => 'nullable|date',
            'status' => 'nullable|string',
            'user_id' => 'nullable|exists:admins,id',
            'quote_currency' => 'nullable|in:MYR,USD,SGD,INR,AED,EUR,GBP',
            'fixed_itinerary_id' => 'nullable|exists:fixed_itineraries,id',
            'fixed_price' => 'nullable|numeric|min:0',
            'fixed_price_currency' => 'nullable|in:MYR,USD,SGD,INR,AED,EUR,GBP',
            'pricing_mode' => 'nullable|in:fixed,live',
            'exchange_rate' => 'nullable|numeric|min:0.00000001',
            'exchange_rate_date' => 'nullable|date',
            'short_description' => 'nullable|string',
            'highlights' => 'nullable|string',
            'inclusions' => 'nullable|string',
            'exclusions' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'travel_date_status' => 'required|in:tbd,flexible,exact',
            'start_date' => ['nullable', 'date', Rule::requiredIf($request->travel_date_status === 'exact')],
            'preferred_travel_month' => 'nullable|date_format:Y-m',
            'travel_date_notes' => 'nullable|string|max:255',
        ]);

        $itineraryData = json_decode($request->itinerary, true);

        // Recalculate duration
        if (is_array($itineraryData)) {
            $itinerary->duration_days = count($itineraryData);
        }

        // Logic to track when last followed up
        if ($request->followup_status && $request->followup_status != $itinerary->followup_status) {
            $itinerary->followed_up_at = now();
        }

        $useFixedPricing = $request->pricing_mode === 'fixed' && (float) $request->fixed_price > 0;
        $fixedPricingData = [
            'fixed_itinerary_id' => $request->filled('fixed_price') && (float) $request->fixed_price > 0 ? $request->fixed_itinerary_id : null,
            'fixed_price' => $request->filled('fixed_price') && (float) $request->fixed_price > 0 ? $request->fixed_price : null,
            'fixed_price_currency' => $request->filled('fixed_price') && (float) $request->fixed_price > 0 ? ($request->fixed_price_currency ?: ($request->quote_currency ?: $itinerary->currency)) : null,
            'pricing_mode' => $useFixedPricing ? 'fixed' : 'live',
        ];
        foreach (['fixed_itinerary_id', 'fixed_price', 'pricing_mode', 'fixed_price_currency'] as $fixedColumn) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('custom_itineraries', $fixedColumn)) {
                unset($fixedPricingData[$fixedColumn]);
            }
        }

        $itinerary->fill(array_merge([
            'itinerary' => $itineraryData,
            'supplier_id' => $request->supplier_id,
            'markup_percentage' => $request->markup_percentage ?? $itinerary->markup_percentage,
            'notes' => $request->notes,
            'client_name' => $request->client_name,
            'title' => $request->title,
            'short_description' => $request->short_description,
            'highlights' => $this->parseProposalList($request->highlights),
            'inclusions' => $this->parseProposalList($request->inclusions),
            'exclusions' => $this->parseProposalList($request->exclusions),
            'terms_conditions' => $request->terms_conditions,
            'quote_currency' => $request->quote_currency ?: $itinerary->currency,
            'exchange_rate' => $request->exchange_rate ?: 1,
            'exchange_rate_date' => $request->exchange_rate_date ?: now()->toDateString(),
            'round_quote' => $request->boolean('round_quote'),
            'country_ids' => is_string($request->country_ids) ? json_decode($request->country_ids, true) : ($request->country_ids ?? $itinerary->country_ids),
            'adults' => $request->adults,
            'children_2_6' => $request->children_2_6 ?? 0,
            'children_6_11' => $request->children_6_11 ?? 0,
            'payment_status' => $request->payment_status ?? 'pending',
            'total_amount_received' => $request->total_amount_received ?? 0,
            'payment_details' => $request->payment_details,
            'followup_status' => $request->followup_status ?? $itinerary->followup_status ?? 'leads',
            'next_followup_date' => $request->next_followup_date,
            'status' => $request->status ?? $itinerary->status,
            'user_id' => $request->user_id ?? $itinerary->user_id,
        ], $fixedPricingData, $this->travelDateData($request)));

        $itinerary->calculatePricing();
        $itinerary->save();

        // Sync Involved Vendors
        if ($request->filled('involved_vendors')) {
            $vendorIds = json_decode($request->involved_vendors, true);
            if (is_array($vendorIds)) {

                // Helper to safely get float
                $safeFloat = function ($val) {
                    return floatval(str_replace(',', '', (string) $val));
                };

                // Resolve supplier_id from item: use stored supplier_id, or look up via service_id
                $resolveSupplierId = function ($item) {
                    if (!empty($item['supplier_id'])) {
                        return $item['supplier_id'];
                    }
                    if (!empty($item['service_id'])) {
                        $service = \App\Models\Service::find($item['service_id']);
                        return $service ? $service->supplier_id : null;
                    }
                    return null;
                };

                // Calculate Costs Per Vendor
                $vendorCosts = [];
                if (is_array($itineraryData)) {
                    foreach ($itineraryData as $day) {
                        // Hotels
                        if (!empty($day['hotels'])) {
                            foreach ($day['hotels'] as $item) {
                                $vid = $resolveSupplierId($item);
                                if ($vid) {
                                    $cost = ($safeFloat($item['price_per_night'] ?? 0) + $safeFloat($item['add_on_price'] ?? 0)) * $safeFloat($item['quantity'] ?? 1);
                                    $vendorCosts[$vid] = ($vendorCosts[$vid] ?? 0) + $cost;
                                }
                            }
                        }
                        // Transport
                        if (!empty($day['transport'])) {
                            foreach ($day['transport'] as $item) {
                                $vid = $resolveSupplierId($item);
                                if ($vid) {
                                    $cost = $safeFloat($item['price'] ?? 0);
                                    $vendorCosts[$vid] = ($vendorCosts[$vid] ?? 0) + $cost;
                                }
                            }
                        }
                        // Activities
                        if (!empty($day['activities'])) {
                            foreach ($day['activities'] as $item) {
                                $vid = $resolveSupplierId($item);
                                if ($vid) {
                                    $et = $item['entry_ticket'] ?? [];
                                    $cost = ($safeFloat($et['adult_price'] ?? 0) * $safeFloat($et['adult_qty'] ?? 0)) +
                                        ($safeFloat($et['child_2_6_price'] ?? 0) * $safeFloat($et['child_2_6_qty'] ?? 0)) +
                                        ($safeFloat($et['child_6_11_price'] ?? 0) * $safeFloat($et['child_6_11_qty'] ?? 0));
                                    $vendorCosts[$vid] = ($vendorCosts[$vid] ?? 0) + $cost;
                                }
                            }
                        }
                        // Tickets/Places
                        if (!empty($day['places'])) {
                            foreach ($day['places'] as $item) {
                                $vid = $resolveSupplierId($item);
                                if ($vid) {
                                    $et = $item['entry_ticket'] ?? [];
                                    $cost = ($safeFloat($et['adult_price'] ?? 0) * $safeFloat($et['adult_qty'] ?? 0)) +
                                        ($safeFloat($et['child_2_6_price'] ?? 0) * $safeFloat($et['child_2_6_qty'] ?? 0)) +
                                        ($safeFloat($et['child_6_11_price'] ?? 0) * $safeFloat($et['child_6_11_qty'] ?? 0));
                                    $vendorCosts[$vid] = ($vendorCosts[$vid] ?? 0) + $cost;
                                }
                            }
                        }
                        // Meals
                        if (!empty($day['meals'])) {
                            foreach ($day['meals'] as $item) {
                                $vid = $resolveSupplierId($item);
                                if ($vid) {
                                    $cost = $safeFloat($item['price'] ?? 0) * $safeFloat($item['quantity'] ?? 1);
                                    $vendorCosts[$vid] = ($vendorCosts[$vid] ?? 0) + $cost;
                                }
                            }
                        }
                        // Spots
                        if (!empty($day['spots'])) {
                            foreach ($day['spots'] as $item) {
                                $vid = $resolveSupplierId($item);
                                if ($vid) {
                                    $cost = $safeFloat($item['price_per_hour'] ?? 0) * $safeFloat($item['hours'] ?? 0);
                                    $vendorCosts[$vid] = ($vendorCosts[$vid] ?? 0) + $cost;
                                }
                            }
                        }
                    }
                }

                // 1. Create/Ensure Exists & Update Cost
                foreach ($vendorIds as $vid) {
                    $supplier = \App\Models\Supplier::find($vid);
                    if (!$supplier) continue;
                    $calculatedCost = $vendorCosts[$vid] ?? 0;

                    $expense = \App\Models\ItineraryExpense::updateOrCreate([
                        'itinerary_id' => $itinerary->id,
                        'itinerary_type' => 'b2b',
                        'supplier_id' => $vid,
                        'description' => 'Involved Vendor',
                    ], [
                        'amount' => $calculatedCost,
                        'category' => $supplier->category_label,
                        'expense_date' => now(),
                        'status' => 'pending'
                    ]);
                }

                // 2. Cleanup only the automatically managed vendor rows.
                \App\Models\ItineraryExpense::where('itinerary_id', $itinerary->id)
                    ->where('itinerary_type', 'b2b')
                    ->where(function ($query) {
                        $query->where('description', 'Involved Vendor')
                            ->orWhere('category', 'Involved Vendor');
                    })
                    ->whereNotIn('supplier_id', $vendorIds)
                    ->delete();
            }
        }

        return redirect()->back()->with('success', 'Itinerary and pricing updated successfully.');
    }

    /**
     * Preview the itinerary
     */
    public function show($id)
    {
        $itinerary = CustomItinerary::with(['agency', 'destination'])->findOrFail($id);
        $enrichedItinerary = ItineraryHelper::enrichItinerary($itinerary->itinerary);

        // Calculate Agency Pricing
        // base_cost is cost to system
        // total_price is cost to client (including markup)
        // We should show the Admin both? Or just the final proposal?
        // Let's show everything to Admin.

        return view('admin.b2b.show', compact('itinerary', 'enrichedItinerary'));
    }

    /**
     * Generate PDF
     */
    public function pdf($id)
    {
        $customItinerary = CustomItinerary::with(['agency', 'destination'])->findOrFail($id);
        $enrichedItinerary = ItineraryHelper::enrichItinerary($customItinerary->itinerary);

        $days = collect($enrichedItinerary)->values()->map(function ($day, $index) {
            $items = collect();
            foreach ($day['places'] ?? [] as $item) $items->push(['item_type' => 'spot', 'title' => $item['attraction_name'] ?? $item['name'] ?? 'Place to visit']);
            foreach ($day['spots'] ?? [] as $item) $items->push(['item_type' => 'spot', 'title' => $item['name'] ?? 'Point of interest']);
            foreach ($day['activities'] ?? [] as $item) $items->push(['item_type' => 'activity', 'title' => $item['name'] ?? 'Activity']);
            foreach ($day['transport'] ?? [] as $item) $items->push(['item_type' => 'transport', 'title' => $item['name'] ?? $item['vehicle_type'] ?? 'Transport']);
            foreach ($day['meals'] ?? [] as $item) $items->push(['item_type' => 'meal', 'title' => $item['name'] ?? 'Meal']);
            $hotels = collect($day['hotels'] ?? []);
            if (!empty($day['hotel']['name']) && $hotels->isEmpty()) $hotels->push($day['hotel']);
            foreach ($hotels as $hotel) $items->push(['item_type' => 'hotel', 'title' => $hotel['name'] ?? 'Hotel', 'room_type' => $hotel['type'] ?? null]);

            return [
                'day_number' => $day['day'] ?? ($index + 1),
                'title' => $day['title'] ?? ('Day ' . ($index + 1)),
                'description' => $day['notes'] ?? $day['description'] ?? null,
                'items' => $items->filter(fn ($item) => !empty($item['title']))->values()->all(),
                'overnight_location' => $hotels->pluck('name')->filter()->join(', '),
            ];
        })->all();

        $total = $customItinerary->customer_total_price;
        $adults = max(1, (int) $customItinerary->adults);
        $childSmall = (int) $customItinerary->children_2_6;
        $childLarge = (int) $customItinerary->children_6_11;
        $weightedPax = $adults + ($childSmall * .25) + ($childLarge * .5);
        $adultPrice = $weightedPax > 0 ? $total / $weightedPax : 0;
        $customerProposal = (object) [
            'title' => $customItinerary->title,
            'client_name' => $customItinerary->client_name ?: ($customItinerary->agency->company_name ?? 'Agency Partner'),
            'duration' => $customItinerary->duration_days . ' Days / ' . max(0, $customItinerary->duration_days - 1) . ' Nights',
            'destination' => $customItinerary->destination,
            'travelers' => $customItinerary->adults . ' Adults' . ($childSmall ? ' + ' . $childSmall . ' Child (2–6)' : '') . ($childLarge ? ' + ' . $childLarge . ' Child (6–11)' : ''),
            'short_description' => $customItinerary->short_description ?: 'A professionally planned travel program prepared for your valued guests.',
            'highlights' => $customItinerary->highlights ?: [],
            'days' => $days,
            'inclusions' => $customItinerary->inclusions ?: ['Accommodation listed in the itinerary', 'Listed transportation and transfers', 'Sightseeing and activities shown above'],
            'exclusions' => $customItinerary->exclusions ?: ['Flights, visas and insurance unless listed', 'Personal expenses and optional activities'],
            'terms_conditions' => $customItinerary->terms_conditions ?: 'Rates and availability are subject to confirmation. Reservations are confirmed after the required payment is received.',
            'currency' => $customItinerary->customer_currency,
            'fixed_price' => $total,
            'adult_price' => $adultPrice,
            'adult_count' => $adults,
            'child_2_6_count' => $childSmall,
            'child_2_6_price' => $adultPrice * .25,
            'child_6_11_count' => $childLarge,
            'child_6_11_price' => $adultPrice * .5,
            'start_date' => $customItinerary->start_date,
            'travel_date_label' => $customItinerary->travel_date_label,
            'quote_id' => $customItinerary->quote_id,
        ];

        $pdf = Pdf::loadView('admin.b2c.pdf', ['itinerary' => $customerProposal])->setPaper('a4', 'portrait');

        $filename = (request()->has('public') ? 'Proposal' : 'Agency_Proposal') . '_' .
            \Illuminate\Support\Str::slug($customItinerary->client_name ?? 'Guest') . '_' .
            now()->format('d_M_Y') . '.pdf';

        return $pdf->download($filename);
    }

    public function whatsapp(Request $request, $id)
    {
        $itinerary = CustomItinerary::with(['agency', 'destination'])->findOrFail($id);
        $mode = $request->get('mode', 'customer');

        // Logic check for Driver/Vendor shares (prevent accidental shares)
        if (($mode === 'driver' || $mode === 'vendor') && $itinerary->status !== 'confirmed') {
            return response()->json(['error' => 'Action Blocked: Itinerary must be CONFIRMED before sharing job sheets.'], 403);
        }

        if ($mode === 'driver') {
            // --- DRIVER JOB SHEET ---
            $text = "*🚖 TOUR JOB SHEET*\n";
            $text .= "----------------------\n";
            $text .= "Ref: " . $itinerary->quote_id . "\n";
            $text .= "Guest: " . ($itinerary->client_name ?? 'Guest') . "\n";
            $text .= "Pax: " . $itinerary->adults . "A + " . ($itinerary->children_2_6 + $itinerary->children_6_11) . "C\n";
            $text .= "Start: " . $itinerary->travel_date_label . "\n";
            $text .= "Duration: " . $itinerary->duration_days . " Days\n\n";

            $data = $itinerary->itinerary ?? [];
            foreach ($data as $day) {
                $text .= "*Day " . ($day['day'] ?? '?') . ":* " . ($day['title'] ?? 'Untitled') . "\n";

                // Tourist Spots (Key for driver)
                if (!empty($day['spots'])) {
                    $spots = [];
                    foreach ($day['spots'] as $s) {
                        if (!empty($s['name']))
                            $spots[] = $s['name'];
                    }
                    if (!empty($spots))
                        $text .= "📍 " . implode(', ', $spots) . "\n";
                } elseif (!empty($day['activities'])) {
                    // Fallback to activities if no spots defined
                    $acts = [];
                    foreach ($day['activities'] as $a) {
                        if (!empty($a['name']))
                            $acts[] = $a['name'];
                    }
                    if (!empty($acts))
                        $text .= "📍 " . implode(', ', $acts) . "\n";
                }

                // Hotel Name (Key for driver)
                if (!empty($day['hotel']['name'])) {
                    $text .= "🏨 Drop/Stay: " . $day['hotel']['name'] . "\n";
                } elseif (!empty($day['hotels'])) {
                    foreach ($day['hotels'] as $h) {
                        if (!empty($h['name']))
                            $text .= "🏨 Drop/Stay: " . $h['name'] . "\n";
                    }
                }

                // Transport details if any
                if (!empty($day['transport'])) {
                    foreach ($day['transport'] as $t) {
                        if (!empty($t['name']))
                            $text .= "🚗 " . $t['name'] . "\n";
                    }
                }

                $text .= "\n";
            }
            $text .= "----------------------\n";
            $text .= "Safe Driving!";

            return response()->json(['text' => $text]);
        }

        // --- CUSTOMER / AGENCY PROPOSAL ---
        $data = $itinerary->itinerary ?? [];
        $text = "✈️ *TOURLIZ HOLIDAYS*\n_Agency Travel Proposal_\n\n";
        $text .= "🌟 *" . $itinerary->title . "*\n";
        $text .= "👤 *Prepared for:* " . ($itinerary->client_name ?: ($itinerary->agency->company_name ?? 'Agency Partner')) . "\n";
        $text .= "📍 *Destination:* " . ($itinerary->destination->name ?? 'N/A') . "\n";
        $text .= "🗓️ *Duration:* " . $itinerary->duration_days . " Days\n";
        $text .= "📅 *Travel date:* " . $itinerary->travel_date_label . "\n";
        $text .= "👥 *Travelers:* " . $itinerary->adults . " Adults";
        if ($itinerary->children_2_6 > 0)
            $text .= ", " . $itinerary->children_2_6 . " Child (2-6y)";
        if ($itinerary->children_6_11 > 0)
            $text .= ", " . $itinerary->children_6_11 . " Child (6-11y)";
        $text .= "\n\n📝 *JOURNEY OVERVIEW*\n" . ($itinerary->short_description ?: 'A professionally planned travel program for your valued guests.') . "\n";
        if (!empty($itinerary->highlights)) {
            $text .= "\n✨ *PACKAGE HIGHLIGHTS*\n";
            foreach ($itinerary->highlights as $item) $text .= "  • {$item}\n";
        }
        $text .= "\n🗺️ *DAY-BY-DAY ITINERARY*\n";

        foreach ($data as $day) {
            $text .= "\n*DAY " . ($day['day'] ?? '?') . ": " . ($day['title'] ?? 'Explore & Experience') . "*\n";
            if (!empty($day['notes'] ?? $day['description'] ?? null)) $text .= trim($day['notes'] ?? $day['description']) . "\n";

            if (!empty($day['hotel']['name'])) {
                $text .= "🏨 Hotel: *" . $day['hotel']['name'] . "* (" . ($day['hotel']['type'] ?? 'Standard') . ")\n";
            }
            if (!empty($day['hotels'])) {
                foreach ($day['hotels'] as $h) {
                    if (!empty($h['name'])) {
                        $text .= "🏨 Hotel: *" . $h['name'] . "* (" . ($h['type'] ?? 'Standard') . ")\n";
                    }
                }
            }

            if (!empty($day['activities'])) {
                foreach ($day['activities'] as $act) {
                    $text .= "📸 " . ($act['name'] ?? 'Activity') . "\n";
                }
            }
            if (!empty($day['spots'])) {
                foreach ($day['spots'] as $spot) {
                    $text .= "📍 " . ($spot['name'] ?? 'Spot') . "\n";
                }
            }
            if (!empty($day['places'])) foreach ($day['places'] as $place) if (!empty($place['name'] ?? $place['attraction_name'] ?? null)) $text .= "📍 " . ($place['name'] ?? $place['attraction_name']) . "\n";
            if (!empty($day['transport'])) foreach ($day['transport'] as $service) if (!empty($service['name'])) $text .= "🚗 " . $service['name'] . "\n";
        }

        $inclusions = $itinerary->inclusions ?: ['Accommodation and services listed above'];
        $exclusions = $itinerary->exclusions ?: ['Items not specifically mentioned'];
        $text .= "\n✅ *PACKAGE INCLUSIONS*\n";
        foreach ($inclusions as $item) $text .= "  ✓ {$item}\n";
        $text .= "\n❌ *PACKAGE EXCLUSIONS*\n";
        foreach ($exclusions as $item) $text .= "  × {$item}\n";

        $adults = (int) $itinerary->adults;
        $c1 = (int) $itinerary->children_2_6;
        $c2 = (int) $itinerary->children_6_11;
        $customerCurrency = $itinerary->customer_currency;
        $customerTotal = $itinerary->customer_total_price;
        $weightedPax = max(1, $adults) + ($c1 * .25) + ($c2 * .5);
        $adultCost = $weightedPax > 0 ? ($customerTotal / $weightedPax) : 0;
        $text .= "\n💰 *PACKAGE PRICE*\n";
        $text .= "• Adult: *{$customerCurrency} " . number_format($adultCost, 2) . " per person*\n";
        if ($c1 > 0) $text .= "• Child (2-6 years): *{$customerCurrency} " . number_format($adultCost * .25, 2) . " per child*\n";
        if ($c2 > 0) $text .= "• Child (6-11 years): *{$customerCurrency} " . number_format($adultCost * .5, 2) . " per child*\n";
        $text .= "\n*TOTAL PACKAGE PRICE*\n*{$customerCurrency} " . number_format($customerTotal, 2) . "*\n";
        $text .= "\n_" . ($itinerary->terms_conditions ?: 'Rates and availability are subject to confirmation.') . "_\n\n";
        $text .= "Thank you for choosing *Tourliz Holidays*.";

        return response()->json(['text' => $text]);
    }

    public function destroy($id)
    {
        try {
            $itinerary = CustomItinerary::findOrFail($id);
            // Delete associated expenses first
            \App\Models\ItineraryExpense::where('itinerary_id', $id)->where('itinerary_type', 'b2b')->delete();
            $itinerary->delete();
            return redirect()->route('admin.b2b-itineraries.index')->with('success', 'Itinerary deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Cannot delete: ' . $e->getMessage());
        }
    }

    public function kanban(Request $request)
    {
        $stages = ['leads', 'waiting', 'interested', 'converted', 'not_interested', 'dead'];

        $allLeads = CustomItinerary::with(['agency', 'destination', 'user'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $columns = [];
        foreach ($stages as $stage) {
            $columns[$stage] = $allLeads->filter(fn($i) => ($i->followup_status ?? 'leads') === $stage)->values();
        }

        return view('admin.b2b.kanban', compact('columns', 'stages'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:leads,waiting,interested,converted,not_interested,dead',
        ]);

        $itinerary = CustomItinerary::findOrFail($id);
        if ($request->status === 'converted' && !$itinerary->start_date) {
            return response()->json([
                'success' => false,
                'message' => 'Set an exact departure date before converting this lead.',
                'errors' => ['start_date' => ['An exact departure date is required before conversion.']],
            ], 422);
        }
        $itinerary->followup_status = $request->status;
        $itinerary->followed_up_at = now();
        $itinerary->save();

        return response()->json(['success' => true, 'status' => $itinerary->followup_status]);
    }

    private function parseProposalList($value): array
    {
        if (is_array($value)) return array_values(array_filter(array_map('trim', $value)));
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $value))));
    }

    private function prepareTravelDateRequest(Request $request, bool $requiresExact = false, ?CustomItinerary $itinerary = null): void
    {
        $status = $request->input('travel_date_status');
        if (!$status) {
            $status = $request->filled('start_date')
                ? 'exact'
                : ($itinerary ? $itinerary->effective_travel_date_status : 'tbd');
        }

        $request->merge(['travel_date_status' => $requiresExact ? 'exact' : $status]);
    }

    private function travelDateData(Request $request): array
    {
        $isExact = $request->travel_date_status === 'exact';

        return [
            'travel_date_status' => $request->travel_date_status,
            'start_date' => $isExact ? $request->start_date : null,
            'preferred_travel_month' => $request->travel_date_status === 'flexible'
                ? $request->preferred_travel_month
                : null,
            'travel_date_notes' => $request->travel_date_notes,
        ];
    }
}
