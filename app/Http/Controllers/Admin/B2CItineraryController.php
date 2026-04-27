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
        return view('admin.b2c.create', compact('destinations', 'admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|exists:countries,id',
            'title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'phone' => 'nullable|string',
            'lead_source' => 'nullable|string',
        ]);

        $itineraryStructure = ItineraryHelper::generateSampleItinerary($request->duration_days, $request->destination_id);

        $itinerary = B2CItinerary::create([
            'user_id' => $request->user_id ?? auth()->id(),
            'destination_id' => $request->destination_id,
            'title' => $request->title,
            'client_name' => $request->client_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'start_date' => $request->start_date,
            'duration_days' => $request->duration_days,
            'itinerary' => $itineraryStructure,
            'status' => 'draft',
            'lead_source' => $request->lead_source ?? 'walk_in',
        ]);

        $itinerary->calculatePricing()->save();

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
            'supplier_id' => 'nullable|exists:suppliers,id',
            'user_id' => 'nullable|exists:admins,id',
        ]);

        $prevStatus = $itinerary->followup_status;

        $itinerary->fill([
            'title' => $request->title,
            'client_name' => $request->client_name,
            'supplier_id' => $request->supplier_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'secondary_phone' => $request->secondary_phone,
            'lead_source' => $request->lead_source,
            'markup_percentage' => $request->markup_percentage ?: 0,
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

        $data = [
            'itinerary' => $itinerary,
            'enrichedItinerary' => $enrichedItinerary,
            'client' => $itinerary->client_name,
            'generated_at' => now()->format('d M Y'),
            'agency' => (object) [
                'company_name' => 'Tourliz Official',
                'whatsapp_number' => '+60 12-345 6789', // Malaysian format
                'logo' => asset('images/logo.png') // Fallback logo path
            ],
            'is_public' => request()->has('public') || request()->get('mode') === 'customer'
        ];

        $pdf = Pdf::loadView('admin.b2b.pdf', $data);
        
        $filename = ($data['is_public'] ? 'Proposal' : 'Internal') . '_' . 
                    \Illuminate\Support\Str::slug(ucwords(strtolower($itinerary->client_name ?? 'Guest'))) . '_' . 
                    now()->format('d_M_Y') . '.pdf';

        return $pdf->download($filename);
    }

    public function whatsapp($id)
    {
        $itinerary = B2CItinerary::with(['destination'])->findOrFail($id);
        $data = $itinerary->itinerary;

        $text = "*PROPOSAL: " . strtoupper($itinerary->title) . "*\n";
        $text .= "Ref ID: " . $itinerary->quote_id . "\n";
        $text .= "Destination: " . ($itinerary->destination->name ?? 'N/A') . "\n";
        $text .= "Duration: " . $itinerary->duration_days . " Days\n";
        $text .= "Client: " . ($itinerary->client_name ?? 'Valued Client') . "\n";
        $text .= "Pax: " . ($itinerary->adults ?: 1) . " Adults";
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

        $text .= "*Total Final Quote:* " . $itinerary->currency . " " . number_format($itinerary->total_price, 2) . "\n";

        $text .= "--- PRICING BREAKDOWN ---\n";
        $perpax = $itinerary->base_cost + $itinerary->markup_amount;
        $text .= "• Adult: " . $itinerary->currency . " " . number_format($perpax, 2) . " x " . $itinerary->adults . " = " . $itinerary->currency . " " . number_format($perpax * $itinerary->adults, 2) . "\n";

        if ($itinerary->children_2_6 > 0) {
            $cRate = $perpax * 0.40;
            $text .= "• Child (2-6y): " . $itinerary->currency . " " . number_format($cRate, 2) . " x " . $itinerary->children_2_6 . " = " . $itinerary->currency . " " . number_format($cRate * $itinerary->children_2_6, 2) . "\n";
        }

        if ($itinerary->children_6_11 > 0) {
            $cRate = $perpax * 0.25;
            $text .= "• Child (6-11y): " . $itinerary->currency . " " . number_format($cRate, 2) . " x " . $itinerary->children_6_11 . " = " . $itinerary->currency . " " . number_format($cRate * $itinerary->children_6_11, 2) . "\n";
        }

        $text .= "Payment Status: " . strtoupper($itinerary->payment_status) . "\n\n";
        $text .= "Thank you for choosing Tourliz!";

        return response()->json(['text' => $text]);
    }

    public function destroy($id)
    {
        B2CItinerary::findOrFail($id)->delete();
        return redirect()->route('admin.b2c-itineraries.index')->with('success', 'B2C lead deleted.');
    }
}
