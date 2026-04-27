<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\CustomItinerary;
use App\Models\Destination;
use App\Helpers\ItineraryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class B2BItineraryController extends Controller
{
    /**
     * Display a listing of B2B itineraries.
     */
    public function index(Request $request)
    {
        $query = CustomItinerary::with(['agency', 'destination', 'expenses']);

        // Search Filters
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%" . str_replace('QT-', '', $search) . "%");
            });
        }

        // Dropdown Filters
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

        if ($request->filled('followup')) {
            $query->where('followup_status', $request->followup);
        }

        $itineraries = $query->orderBy('updated_at', 'desc')->paginate(15);
        $destinations = \App\Models\Country::orderBy('name')->get();

        return view('admin.b2b.index', compact('itineraries', 'destinations'));
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
        $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'destination_id' => 'required|exists:countries,id',
            'title' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
        ]);

        // Generate blank/sample itinerary structure
        $itineraryStructure = ItineraryHelper::generateSampleItinerary($request->duration_days, $request->destination_id);

        // Use a simpler approach: Initialize empty structure
        foreach ($itineraryStructure as &$day) {
            $day['places'] = [];
            // Keep structure clean
        }

        $itinerary = CustomItinerary::create([
            'user_id' => $request->user_id ?? auth()->id(),
            'agency_id' => $request->agency_id,
            'destination_id' => $request->destination_id,
            'title' => $request->title,
            'client_name' => $request->client_name,
            'duration_days' => $request->duration_days,
            'start_date' => $request->start_date,
            'itinerary' => $itineraryStructure,
            'status' => 'draft',
            'markup_percentage' => 10.00, // Default markup
        ]);

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
        $countries = \App\Models\Country::orderBy('name')->get();
        // Here destinations refers to cities in our destinations table
        $destinations = \App\Models\Destination::orderBy('city')->get();
        $admins = \App\Models\Admin::orderBy('name')->get();

        // We need attractions too for the builder
        $attractions = \App\Models\Attraction::orderBy('name')->get();

        return view('admin.b2b.edit', compact('itinerary', 'countries', 'destinations', 'attractions', 'admins'));
    }

    /**
     * Update the itinerary details from the builder.
     */
    public function update(Request $request, $id)
    {
        $itinerary = CustomItinerary::findOrFail($id);

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

        $itinerary->fill([
            'itinerary' => $itineraryData,
            'supplier_id' => $request->supplier_id,
            'markup_percentage' => $request->markup_percentage ?? $itinerary->markup_percentage,
            'notes' => $request->notes,
            'client_name' => $request->client_name,
            'title' => $request->title,
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
        ]);

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

                // Helper to resolve supplier ID
                $getSupplierId = function ($item, $modelClass, $nameField = 'name') {
                    if (!empty($item['supplier_id']))
                        return $item['supplier_id'];
                    if (!empty($item[$nameField])) {
                        $record = $modelClass::where($nameField, $item[$nameField])->first();
                        return $record ? $record->supplier_id : null;
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
                                $vid = $getSupplierId($item, \App\Models\Hotel::class, 'name');
                                if ($vid) {
                                    $cost = ($safeFloat($item['price_per_night'] ?? 0) + $safeFloat($item['add_on_price'] ?? 0)) * $safeFloat($item['quantity'] ?? 1);
                                    $vendorCosts[$vid] = ($vendorCosts[$vid] ?? 0) + $cost;
                                }
                            }
                        }
                        // Transport
                        if (!empty($day['transport'])) {
                            foreach ($day['transport'] as $item) {
                                $vid = $getSupplierId($item, \App\Models\Transport::class, 'name');
                                if ($vid) {
                                    $cost = $safeFloat($item['price'] ?? 0);
                                    $vendorCosts[$vid] = ($vendorCosts[$vid] ?? 0) + $cost;
                                }
                            }
                        }
                        // Activities
                        if (!empty($day['activities'])) {
                            foreach ($day['activities'] as $item) {
                                $vid = $getSupplierId($item, \App\Models\Activity::class, 'name');
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
                                $vid = $getSupplierId($item, \App\Models\EntryTicket::class, 'attraction_name');
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
                                if (!empty($item['supplier_id'])) {
                                    $vid = $item['supplier_id'];
                                    $cost = $safeFloat($item['price'] ?? 0) * $safeFloat($item['quantity'] ?? 1);
                                    $vendorCosts[$vid] = ($vendorCosts[$vid] ?? 0) + $cost;
                                }
                            }
                        }
                        // Spots
                        if (!empty($day['spots'])) {
                            foreach ($day['spots'] as $item) {
                                $vid = $getSupplierId($item, \App\Models\TouristSpot::class, 'name');
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
                    $calculatedCost = $vendorCosts[$vid] ?? 0;

                    $expense = \App\Models\ItineraryExpense::firstOrCreate([
                        'itinerary_id' => $itinerary->id,
                        'itinerary_type' => 'b2b',
                        'supplier_id' => $vid
                    ], [
                        'amount' => $calculatedCost,
                        'category' => 'Involved Vendor',
                        'description' => 'Involved Vendor Cost',
                        'expense_date' => now(),
                        'status' => 'pending'
                    ]);

                    // If expense exists but amount is 0 (probably auto-created before customization), update it
                    // Only update if it's strictly "Involved Vendor" category to avoid overwriting specific expenses
                    if ($expense->wasRecentlyCreated || ($expense->category === 'Involved Vendor' && $expense->amount == 0 && $calculatedCost > 0)) {
                        $expense->amount = $calculatedCost;
                        $expense->save();
                    }
                }

                // 2. Cleanup Unchecked (Only 0-cost auto-added ones or Involved Vendor ones)
                \App\Models\ItineraryExpense::where('itinerary_id', $itinerary->id)
                    ->where('itinerary_type', 'b2b')
                    ->where('category', 'Involved Vendor')
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

        $data = [
            'itinerary' => $customItinerary,
            'enrichedItinerary' => $enrichedItinerary,
            'agency' => $customItinerary->agency, // This is the Agency model now
            'client' => $customItinerary->client_name,
            'generated_at' => now()->format('d M Y'),
            'is_public' => request()->has('public') || request()->get('mode') === 'customer'
        ];

        $pdf = Pdf::loadView('admin.b2b.pdf', $data);

        $filename = ($data['is_public'] ? 'Proposal' : 'Internal') . '_' . 
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
            $text .= "Start: " . ($itinerary->start_date ? $itinerary->start_date->format('d M Y') : 'TBA') . "\n";
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

        // --- CUSTOMER PROPOSAL ---
        $data = $itinerary->itinerary;
        $text = "*PROPOSAL: " . strtoupper($itinerary->title) . "*\n";
        $text .= "Ref ID: " . $itinerary->quote_id . "\n";
        $text .= "Destination: " . ($itinerary->destination->name ?? 'N/A') . "\n";
        $text .= "Duration: " . $itinerary->duration_days . " Days\n";
        $text .= "Client: " . ($itinerary->client_name ?? 'Valued Client') . "\n";
        $text .= "Pax: " . $itinerary->adults . " Adults";
        if ($itinerary->children_2_6 > 0)
            $text .= ", " . $itinerary->children_2_6 . " Child (2-6y)";
        if ($itinerary->children_6_11 > 0)
            $text .= ", " . $itinerary->children_6_11 . " Child (6-11y)";
        $text .= "\n\n";

        foreach ($data as $day) {
            $text .= "*Day " . ($day['day'] ?? '?') . ": " . ($day['title'] ?? 'Untitled') . "*\n";

            if (!empty($day['hotel']['name'])) {
                $text .= "🏨 Hotel: " . $day['hotel']['name'] . " (" . ($day['hotel']['type'] ?? 'Standard') . ")\n";
            }
            if (!empty($day['hotels'])) {
                foreach ($day['hotels'] as $h) {
                    if (!empty($h['name'])) {
                        $text .= "🏨 Hotel: " . $h['name'] . " (" . ($h['type'] ?? 'Standard') . ")\n";
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

            $text .= "\n";
        }

        $text .= "*Total Final Quote:* " . $itinerary->currency . " " . number_format($itinerary->total_price, 2) . "\n";
        $text .= "Status: " . ucfirst($itinerary->status) . "\n\n";
        $text .= "Link: " . route('admin.b2b-itineraries.pdf', $itinerary->id) . "?public=1\n";

        return response()->json(['text' => $text]);
    }

    public function destroy($id)
    {
        $itinerary = CustomItinerary::findOrFail($id);
        $itinerary->delete();
        return redirect()->route('admin.b2b-itineraries.index')->with('success', 'Itinerary deleted.');
    }
}
