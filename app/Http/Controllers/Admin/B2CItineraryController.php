<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\B2CItinerary;
use App\Models\Destination;
use App\Helpers\ItineraryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class B2CItineraryController extends Controller
{
    public function index(Request $request)
    {
        $query = B2CItinerary::with(['destination']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%" . str_replace('B2C-', '', $search) . "%");
            });
        }

        if ($request->filled('destination_id'))
            $query->where('destination_id', $request->destination_id);
        if ($request->filled('year'))
            $query->whereYear('created_at', $request->year);
        if ($request->filled('month'))
            $query->whereMonth('created_at', $request->month);
        if ($request->filled('status'))
            $query->where('followup_status', $request->status);

        $itineraries = $query->orderBy('updated_at', 'desc')->paginate(15);
        $destinations = \App\Models\Country::orderBy('name')->get();

        return view('admin.b2c.index', compact('itineraries', 'destinations'));
    }

    public function create(Request $request)
    {
        $destinations = \App\Models\Country::orderBy('name')->get();
        $admins = \App\Models\Admin::orderBy('name')->get();
        $groupPackages = \App\Models\GroupPackage::where('status', true)->orderBy('name')->get();
        $fixedItineraries = \App\Models\FixedItinerary::where('status', 'active')->orderBy('title')->get();
        return view('admin.b2c.create', compact('destinations', 'admins', 'groupPackages', 'fixedItineraries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|exists:countries,id',
            'fixed_itinerary_id' => 'nullable|exists:fixed_itineraries,id',
            'group_package_id' => 'nullable|exists:group_packages,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'phone' => 'nullable|string',
            'lead_source' => 'nullable|string',
        ]);

        $itineraryStructure = null;
        $masterContent = [];

        // 1. Check if a Fixed Itinerary (Master Package) was selected (Optional)
        if ($request->filled('fixed_itinerary_id')) {
            $fi = \App\Models\FixedItinerary::find($request->fixed_itinerary_id);
            if ($fi && !empty($fi->days)) {
                $masterContent = [
                    'short_description' => $fi->short_description ?: $fi->itinerary_description,
                    'highlights' => $fi->highlights ?: [],
                    'inclusions' => $fi->inclusions ?: [],
                    'exclusions' => $fi->exclusions ?: [],
                    'terms_conditions' => $fi->terms_conditions,
                ];
                $rawDays = is_array($fi->days) ? $fi->days : (json_decode($fi->days, true) ?: []);
                if (!empty($rawDays)) {
                    $itineraryStructure = array_map(function ($day, $idx) use ($fi) {
                        $dayNum = $day['day_number'] ?? ($idx + 1);
                        $hotels = [];
                        $transports = [];
                        $spots = [];
                        $activities = [];
                        $meals = [];

                        if (!empty($day['items']) && is_array($day['items'])) {
                            foreach ($day['items'] as $item) {
                                $type = strtolower($item['item_type'] ?? '');
                                $itemTitle = $item['title'] ?? '';
                                $itemPrice = (float)($item['price'] ?? 0);
                                $suppId = !empty($item['supplier_id']) ? (int)$item['supplier_id'] : null;

                                if ($type === 'hotel') {
                                    $hotels[] = [
                                        'name' => $itemTitle ?: 'Hotel Accommodation',
                                        'type' => $item['room_type'] ?: 'Standard',
                                        'hotel_id' => $item['item_id'] ?? null,
                                        'hotel_room_id' => null,
                                        'price_per_night' => $itemPrice,
                                        'currency' => $fi->currency ?: 'MYR',
                                        'quantity' => 1,
                                        'add_on_price' => 0,
                                        'service_id' => null,
                                        'supplier_id' => $suppId,
                                    ];
                                } elseif ($type === 'transport') {
                                    $transports[] = [
                                        'name' => $itemTitle ?: 'Vehicle / Transfer',
                                        'transport_id' => $item['item_id'] ?? null,
                                        'vehicle_type' => $item['room_type'] ?: 'Transfer',
                                        'price' => $itemPrice,
                                        'currency' => $fi->currency ?: 'MYR',
                                        'service_id' => null,
                                        'supplier_id' => $suppId,
                                    ];
                                } elseif ($type === 'spot' || $type === 'tourist_spot') {
                                    $spots[] = [
                                        'name' => $itemTitle ?: 'Attraction / Spot',
                                        'spot_id' => $item['item_id'] ?? null,
                                        'tourist_spot_id' => $item['item_id'] ?? null,
                                        'description' => $item['description'] ?? '',
                                        'hours' => 2,
                                        'price_per_hour' => 0,
                                        'service_id' => null,
                                        'supplier_id' => $suppId,
                                    ];
                                } elseif ($type === 'activity' || $type === 'ticket') {
                                    $activities[] = [
                                        'name' => $itemTitle ?: 'Tour / Activity',
                                        'activity_id' => $item['item_id'] ?? null,
                                        'description' => $item['description'] ?? '',
                                        'entry_ticket' => [
                                            'adult_price' => $itemPrice,
                                            'adult_qty' => 1,
                                            'child_2_6_price' => 0,
                                            'child_2_6_qty' => 0,
                                            'child_6_11_price' => 0,
                                            'child_6_11_qty' => 0,
                                            'currency' => $fi->currency ?: 'MYR',
                                        ],
                                        'service_id' => null,
                                        'supplier_id' => $suppId,
                                    ];
                                } elseif ($type === 'meal') {
                                    $meals[] = [
                                        'name' => $itemTitle ?: 'Meal Service',
                                        'price' => $itemPrice,
                                        'quantity' => 1,
                                        'currency' => $fi->currency ?: 'MYR',
                                        'service_id' => null,
                                        'supplier_id' => $suppId,
                                    ];
                                }
                            }
                        }

                        return [
                            'day' => $dayNum,
                            'title' => $day['title'] ?? ('Day ' . $dayNum),
                            'description' => $day['description'] ?? '',
                            'hotels' => $hotels,
                            'transport' => $transports,
                            'places' => [],
                            'spots' => $spots,
                            'activities' => $activities,
                            'meals' => $meals,
                            'notes' => $day['description'] ?? ($day['notes'] ?? ''),
                        ];
                    }, $rawDays, array_keys($rawDays));
                }
            }
        }

        // 2. Fallback: Check if an Operations Group Tour Master was selected (Optional)
        if (empty($itineraryStructure) && $request->filled('group_package_id')) {
            $gp = \App\Models\GroupPackage::find($request->group_package_id);
            if ($gp && !empty($gp->itinerary)) {
                $rawItin = is_array($gp->itinerary) ? $gp->itinerary : (json_decode($gp->itinerary, true) ?: []);
                if (!empty($rawItin)) {
                    $itineraryStructure = array_map(function ($day, $idx) {
                        return [
                            'day' => $day['day'] ?? ($idx + 1),
                            'title' => $day['title'] ?? ('Day ' . ($idx + 1)),
                            'hotels' => !empty($day['hotel']['name']) ? [[
                                'name' => $day['hotel']['name'],
                                'type' => $day['hotel']['type'] ?? 'Standard',
                                'hotel_id' => $day['hotel']['hotel_id'] ?? null,
                                'hotel_room_id' => $day['hotel']['hotel_room_id'] ?? null,
                                'price_per_night' => (float)($day['hotel']['price_per_night'] ?? 0),
                                'currency' => $day['hotel']['currency'] ?? 'MYR',
                                'quantity' => 1,
                                'add_on_price' => 0,
                                'service_id' => null,
                                'supplier_id' => $day['hotel']['supplier_id'] ?? null,
                            ]] : [],
                            'transport' => array_map(function ($t) {
                                return [
                                    'name' => $t['mode'] ?? $t['name'] ?? $t['type'] ?? 'Transport',
                                    'transport_id' => $t['transport_id'] ?? null,
                                    'vehicle_type' => $t['vehicle_type'] ?? $t['mode'] ?? 'Transfer',
                                    'price' => (float)($t['price'] ?? 0),
                                    'currency' => $t['currency'] ?? 'MYR',
                                    'service_id' => null,
                                    'supplier_id' => $t['supplier_id'] ?? null,
                                ];
                            }, $day['transport'] ?? []),
                            'places' => [],
                            'spots' => array_map(function ($p) {
                                $name = is_string($p) ? $p : ($p['name'] ?? $p['attraction_name'] ?? 'Spot');
                                return [
                                    'name' => $name,
                                    'spot_id' => is_array($p) ? ($p['spot_id'] ?? $p['id'] ?? null) : null,
                                    'tourist_spot_id' => is_array($p) ? ($p['spot_id'] ?? $p['id'] ?? null) : null,
                                    'description' => '',
                                    'hours' => 2,
                                    'price_per_hour' => 0,
                                    'service_id' => null,
                                    'supplier_id' => null,
                                ];
                            }, $day['places'] ?? []),
                            'activities' => array_map(function ($a) {
                                return [
                                    'name' => $a['name'] ?? 'Activity',
                                    'activity_id' => $a['activity_id'] ?? null,
                                    'description' => $a['description'] ?? '',
                                    'entry_ticket' => $a['entry_ticket'] ?? [
                                        'adult_price' => (float)($a['price'] ?? 0),
                                        'adult_qty' => 1,
                                        'child_2_6_price' => 0,
                                        'child_2_6_qty' => 0,
                                        'child_6_11_price' => 0,
                                        'child_6_11_qty' => 0,
                                        'currency' => 'MYR',
                                    ],
                                    'service_id' => null,
                                    'supplier_id' => $a['supplier_id'] ?? null,
                                ];
                            }, $day['activities'] ?? []),
                            'meals' => !empty($day['meals']) && is_array($day['meals']) ? array_values(array_filter(array_map(function ($mType, $mDesc) {
                                if ($mDesc && stripos($mDesc, 'included') !== false) {
                                    return [
                                        'name' => '[' . strtoupper($mType) . '] Operational Meal',
                                        'price' => 0,
                                        'quantity' => 1,
                                        'currency' => 'MYR',
                                        'service_id' => null,
                                        'supplier_id' => null,
                                    ];
                                }
                                return null;
                            }, array_keys($day['meals']), array_values($day['meals'])))) : [],
                            'notes' => $day['notes'] ?? '',
                        ];
                    }, $rawItin, array_keys($rawItin));
                }
            }
        }

        if (empty($itineraryStructure)) {
            $itineraryStructure = ItineraryHelper::generateSampleItinerary($request->duration_days, $request->destination_id);
        }

        $itinerary = B2CItinerary::create([
            'user_id' => $request->user_id ?? auth()->id(),
            'destination_id' => $request->destination_id,
            'country_ids' => $request->country_ids ?? [],
            'title' => $request->title,
            'short_description' => $masterContent['short_description'] ?? null,
            'client_name' => $request->client_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'start_date' => $request->start_date,
            'duration_days' => $request->duration_days,
            'itinerary' => $itineraryStructure,
            'highlights' => $masterContent['highlights'] ?? [],
            'inclusions' => $masterContent['inclusions'] ?? [],
            'exclusions' => $masterContent['exclusions'] ?? [],
            'terms_conditions' => $masterContent['terms_conditions'] ?? null,
            'status' => 'draft',
            'lead_source' => $request->lead_source ?? 'walk_in',
        ]);

        $itinerary->calculatePricing()->save();

        // Staff alerts must never block creation of the customer lead.
        try {
            app(\App\Services\LeadWhatsAppNotifier::class)->notifyNewLead($itinerary);
        } catch (\Throwable $e) {
            \Log::error('Unable to send new walk-in lead WhatsApp notification.', [
                'lead_id' => $itinerary->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.b2c-itineraries.edit', $itinerary->id)
            ->with('success', 'Walk-in lead created. Now build the itinerary.');
    }

    public function edit($id)
    {
        $itinerary = B2CItinerary::with(['destination'])->findOrFail($id);
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = \App\Models\Destination::orderBy('city')->get();
        $admins = \App\Models\Admin::orderBy('name')->get();
        return view('admin.b2c.edit', compact('itinerary', 'countries', 'destinations', 'admins'));
    }

    public function update(Request $request, $id)
    {
        $itinerary = B2CItinerary::findOrFail($id);

        $request->validate([
            'itinerary' => 'required|json',
            'title' => 'required|string',
            'client_name' => 'required|string',
            'adults' => 'required|integer|min:1',
            'quote_currency' => 'nullable|in:MYR,USD,SGD,INR,AED,EUR,GBP',
            'exchange_rate' => 'nullable|numeric|min:0.00000001',
            'exchange_rate_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'user_id' => 'nullable|exists:admins,id',
            'short_description' => 'nullable|string',
            'highlights' => 'nullable|string',
            'inclusions' => 'nullable|string',
            'exclusions' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ]);

        $prevStatus = $itinerary->followup_status;

        $itinerary->fill([
            'title' => $request->title,
            'short_description' => $request->short_description,
            'highlights' => $this->parseProposalList($request->highlights),
            'inclusions' => $this->parseProposalList($request->inclusions),
            'exclusions' => $this->parseProposalList($request->exclusions),
            'terms_conditions' => $request->terms_conditions,
            'client_name' => $request->client_name,
            'supplier_id' => $request->supplier_id,
            'country_ids' => $request->country_ids ?? $itinerary->country_ids,
            'email' => $request->email,
            'phone' => $request->phone,
            'secondary_phone' => $request->secondary_phone,
            'lead_source' => $request->lead_source,
            'markup_percentage' => $request->markup_percentage ?: 0,
            'quote_currency' => $request->quote_currency ?: $itinerary->currency,
            'exchange_rate' => $request->exchange_rate ?: 1,
            'exchange_rate_date' => $request->exchange_rate_date ?: now()->toDateString(),
            'round_quote' => $request->boolean('round_quote'),
            'adults' => $request->adults,
            'children_2_6' => $request->children_2_6 ?: 0,
            'children_6_11' => $request->children_6_11 ?: 0,
            'payment_status' => $request->payment_status ?: 'pending',
            'total_amount_received' => $request->total_amount_received ?: 0,
            'payment_details' => $request->payment_details,
            'followup_status' => $request->followup_status ?: 'leads',
            'next_followup_date' => $request->next_followup_date,
            'start_date' => $request->start_date,
            'duration_days' => $request->duration_days,
            'notes' => $request->notes,
            'user_id' => $request->user_id ?? $itinerary->user_id,
        ]);

        $itinerary->itinerary = json_decode($request->itinerary, true);

        if ($itinerary->followup_status != $prevStatus) {
            $itinerary->followed_up_at = now();
        }

        // Final safety check for non-nullable DB columns
        if (empty($itinerary->payment_status))
            $itinerary->payment_status = 'pending';
        if (empty($itinerary->followup_status))
            $itinerary->followup_status = 'leads';
        if (empty($itinerary->status))
            $itinerary->status = 'draft';

        $itinerary->calculatePricing()->save();

        // Sync Involved Vendors
        if ($request->filled('involved_vendors')) {
            $vendorIds = json_decode($request->involved_vendors, true);
            if (is_array($vendorIds)) {
                // 1. Create/Ensure Exists
                foreach ($vendorIds as $vid) {
                    \App\Models\ItineraryExpense::firstOrCreate([
                        'itinerary_id' => $itinerary->id,
                        'itinerary_type' => 'b2c',
                        'supplier_id' => $vid
                    ], [
                        'amount' => 0,
                        'category' => 'Involved Vendor',
                        'description' => 'Involved Vendor',
                        'expense_date' => now(),
                        'status' => 'pending'
                    ]);
                }
                // 2. Cleanup Unchecked (Only 0-cost auto-added ones)
                \App\Models\ItineraryExpense::where('itinerary_id', $itinerary->id)
                    ->where('itinerary_type', 'b2c')
                    ->where('category', 'Involved Vendor')
                    ->where('amount', 0)
                    ->whereNotIn('supplier_id', $vendorIds)
                    ->delete();
            }
        }

        return redirect()->back()->with('success', 'B2C Proposal updated successfully.');
    }

    public function pdf($id)
    {
        $itinerary = B2CItinerary::with(['destination'])->findOrFail($id);
        $enrichedItinerary = ItineraryHelper::enrichItinerary($itinerary->itinerary);

        $days = collect($enrichedItinerary)->values()->map(function ($day, $index) {
            $items = collect();
            foreach ($day['places'] ?? [] as $item) {
                $items->push(['item_type' => 'spot', 'title' => $item['attraction_name'] ?? $item['name'] ?? 'Place to visit']);
            }
            foreach ($day['spots'] ?? [] as $item) {
                $items->push(['item_type' => 'spot', 'title' => $item['name'] ?? 'Point of interest']);
            }
            foreach ($day['activities'] ?? [] as $item) {
                $items->push(['item_type' => 'activity', 'title' => $item['name'] ?? 'Activity']);
            }
            foreach ($day['transport'] ?? [] as $item) {
                $items->push(['item_type' => 'transport', 'title' => $item['name'] ?? 'Transport']);
            }
            foreach ($day['meals'] ?? [] as $item) {
                $items->push(['item_type' => 'meal', 'title' => $item['name'] ?? 'Meal']);
            }

            $hotels = collect($day['hotels'] ?? []);
            if (!empty($day['hotel']['name']) && $hotels->isEmpty()) {
                $hotels->push($day['hotel']);
            }
            foreach ($hotels as $hotel) {
                $items->push([
                    'item_type' => 'hotel',
                    'title' => $hotel['name'] ?? 'Hotel',
                    'room_type' => $hotel['type'] ?? null,
                ]);
            }

            return [
                'day_number' => $day['day'] ?? ($index + 1),
                'title' => $day['title'] ?? ('Day ' . ($index + 1)),
                'description' => $day['notes'] ?? null,
                'items' => $items->filter(fn ($item) => !empty($item['title']))->values()->all(),
                'overnight_location' => $hotels->pluck('name')->filter()->join(', '),
            ];
        })->all();

        $mealIncluded = collect($enrichedItinerary)->contains(fn ($day) => !empty($day['meals']));
        $customerProposal = (object) [
            'title' => $itinerary->title,
            'client_name' => $itinerary->client_name,
            'duration' => $itinerary->duration_days . ' Days / ' . max(0, $itinerary->duration_days - 1) . ' Nights',
            'destination' => $itinerary->destination,
            'countries' => $itinerary->countries,
            'travelers' => $itinerary->adults . ' Adults' .
                ($itinerary->children_2_6 ? ' + ' . $itinerary->children_2_6 . ' Child (2–6)' : '') .
                ($itinerary->children_6_11 ? ' + ' . $itinerary->children_6_11 . ' Child (6–11)' : ''),
            'short_description' => $itinerary->short_description ?: 'A thoughtfully planned journey prepared especially for ' . $itinerary->client_name . '.',
            'itinerary_description' => null,
            'days' => $days,
            'highlights' => $itinerary->highlights ?: [],
            'inclusions' => $itinerary->inclusions ?: array_values(array_filter([
                'Accommodation as listed in the itinerary',
                'Transportation and transfers shown in the itinerary',
                'Sightseeing and activities specifically listed',
                $mealIncluded ? 'Meals specifically listed in the day plan' : null,
            ])),
            'exclusions' => $itinerary->exclusions ?: [
                'Flights, visas and travel insurance unless expressly listed',
                'Personal expenses and optional activities',
                'Anything not specifically mentioned under inclusions',
            ],
            'terms_conditions' => $itinerary->terms_conditions ?: 'This proposal is valid for 7 days from the issue date. Rates and availability are subject to confirmation. Reservations are confirmed only after the required payment is received.',
            'currency' => $itinerary->customer_currency,
            'fixed_price' => $itinerary->customer_total_price,
            'converted_prices' => [],
            'start_date' => $itinerary->start_date,
            'quote_id' => $itinerary->quote_id,
        ];

        $pdf = Pdf::loadView('admin.b2c.pdf', [
            'itinerary' => $customerProposal,
        ])->setPaper('a4', 'portrait');

        $filename = 'Proposal_' .
            \Illuminate\Support\Str::slug(ucwords(strtolower($itinerary->client_name ?? 'Guest'))) . '_' .
            now()->format('d_M_Y') . '.pdf';

        return $pdf->download($filename);
    }

    public function whatsapp($id)
    {
        $itinerary = B2CItinerary::with(['destination'])->findOrFail($id);
        $data = $itinerary->itinerary;

        $text = "✈️ *TOURLIZ HOLIDAYS*\n";
        $text .= "_Curated Travel Proposal_\n\n";
        $text .= "🌟 *" . $itinerary->title . "*\n";
        $text .= "👤 *Prepared for:* " . ($itinerary->client_name ?? 'Valued Client') . "\n";
        $text .= "🗓️ *Duration:* " . $itinerary->duration_days . " Days / " . max(0, $itinerary->duration_days - 1) . " Nights\n";
        $text .= "📍 *Destination:* " . ($itinerary->destination->name ?? 'N/A') . "\n";
        $text .= "📅 *Travel date:* " . ($itinerary->start_date ? $itinerary->start_date->format('d M Y') : 'To be confirmed') . "\n";
        $text .= "👥 *Travelers:* " . ($itinerary->adults ?: 1) . " Adults";
        if ($itinerary->children_2_6 > 0)
            $text .= ", " . $itinerary->children_2_6 . " Child (2-6y)";
        if ($itinerary->children_6_11 > 0)
            $text .= ", " . $itinerary->children_6_11 . " Child (6-11y)";
        $text .= "\n\n📝 *JOURNEY OVERVIEW*\n";
        $text .= ($itinerary->short_description ?: "A thoughtfully planned journey prepared especially for " . ($itinerary->client_name ?? 'you') . ".") . "\n";
        if (!empty($itinerary->highlights)) {
            $text .= "\n✨ *PACKAGE HIGHLIGHTS*\n";
            foreach ($itinerary->highlights as $highlight) {
                $text .= "  • " . $highlight . "\n";
            }
        }
        $text .= "\n";
        $text .= "🗺️ *DAY-BY-DAY ITINERARY*\n";

        foreach ($data as $day) {
            $text .= "\n*DAY " . ($day['day'] ?? '?') . " — " . ($day['title'] ?? 'Explore & Experience') . "*\n";
            if (!empty($day['notes'] ?? $day['description'] ?? null)) {
                $text .= trim($day['notes'] ?? $day['description']) . "\n";
            }

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

            if (!empty($day['meals'])) {
                $text .= "🍱 Meals: ";
                $meals = [];
                foreach ($day['meals'] as $m)
                    $meals[] = $m['name'] ?? 'Meal';
                $text .= implode(', ', $meals) . "\n";
            }

            if (!empty($day['transport'])) {
                foreach ($day['transport'] as $tran) {
                    $text .= "🚗 " . ($tran['name'] ?? 'Transport') . "\n";
                }
            }

            $text .= "\n";
        }

        $customerCurrency = $itinerary->customer_currency;
        $customerTotal = $itinerary->customer_total_price;
        $inclusions = $itinerary->inclusions ?: ['Accommodation listed in the itinerary', 'Listed transportation and transfers', 'Sightseeing and activities shown above'];
        $exclusions = $itinerary->exclusions ?: ['Flights, visas and travel insurance unless listed', 'Personal expenses and optional activities'];
        $text .= "✅ *PACKAGE INCLUSIONS*\n";
        foreach ($inclusions as $item) $text .= "  ✓ " . $item . "\n";
        $text .= "\n❌ *PACKAGE EXCLUSIONS*\n";
        foreach ($exclusions as $item) $text .= "  × " . $item . "\n";
        $text .= "\n";
        $text .= "💰 *TOTAL PACKAGE PRICE*\n";
        $text .= "*" . $customerCurrency . " " . number_format($customerTotal, 2) . "*\n";

        $adults = (int) $itinerary->adults;
        $c1 = (int) $itinerary->children_2_6;
        $c2 = (int) $itinerary->children_6_11;
        $hasChildren = ($c1 > 0 || $c2 > 0);

        if ($hasChildren) {
            $weightedPax = ($adults * 1.0) + ($c1 * 0.25) + ($c2 * 0.50);
            $adultCost = $weightedPax > 0 ? ($customerTotal / $weightedPax) : 0;
            $child26Cost = $adultCost * 0.25;
            $child611Cost = $adultCost * 0.50;

            $text .= "--- PRICING BREAKDOWN ---\n";
            $text .= "• Adult: " . $customerCurrency . " " . number_format($adultCost, 2) . " x " . $adults . " = " . $customerCurrency . " " . number_format($adultCost * $adults, 2) . "\n";
            if ($c1 > 0) {
                $text .= "• Child (2-6y): " . $customerCurrency . " " . number_format($child26Cost, 2) . " x " . $c1 . " = " . $customerCurrency . " " . number_format($child26Cost * $c1, 2) . "\n";
            }
            if ($c2 > 0) {
                $text .= "• Child (6-11y): " . $customerCurrency . " " . number_format($child611Cost, 2) . " x " . $c2 . " = " . $customerCurrency . " " . number_format($child611Cost * $c2, 2) . "\n";
            }
            $text .= "\n";
        }

        $text .= "_" . ($itinerary->terms_conditions ?: 'Rates and availability are subject to confirmation.') . "_\n\n";
        $text .= "*Contact Tourliz:*\n";
        $text .= "📞 WhatsApp:\n";
        $text .= "  - Malaysia: +60102339314 / +60189836532\n";
        $text .= "  - India: +91 96338 50694\n";
        $text .= "🌐 Website: www.tourliz.com\n";
        $text .= "📸 Socials: @tourliz\n\n";
        $text .= "Thank you for choosing Tourliz!";

        return response()->json(['text' => $text]);
    }

    public function kanban(Request $request)
    {
        $stages = ['leads', 'quoted', 'interested', 'confirmed', 'travelled'];

        $allLeads = B2CItinerary::with(['destination', 'user'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $columns = [];
        foreach ($stages as $stage) {
            $columns[$stage] = $allLeads->filter(fn($i) => ($i->followup_status ?? 'leads') === $stage)->values();
        }

        return view('admin.b2c.kanban', compact('columns', 'stages'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:leads,quoted,interested,confirmed,travelled',
        ]);

        $itinerary = B2CItinerary::findOrFail($id);
        $itinerary->followup_status = $request->status;
        $itinerary->followed_up_at = now();
        $itinerary->save();

        return response()->json(['success' => true, 'status' => $itinerary->followup_status]);
    }

    public function destroy($id)
    {
        try {
            // Delete associated expenses first
            \App\Models\ItineraryExpense::where('itinerary_id', $id)->where('itinerary_type', 'b2c')->delete();
            B2CItinerary::findOrFail($id)->delete();
            return redirect()->route('admin.b2c-itineraries.index')->with('success', 'B2C lead deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Cannot delete: ' . $e->getMessage());
        }
    }

    private function parseProposalList($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $value))));
    }
}
