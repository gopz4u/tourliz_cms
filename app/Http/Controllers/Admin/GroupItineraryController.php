<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupItinerary;
use App\Models\Destination;
use App\Helpers\ItineraryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class GroupItineraryController extends Controller
{
    public function index(Request $request)
    {
        $query = GroupItinerary::with(['destination']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%" . str_replace('GRP-', '', $search) . "%");
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

        return view('admin.group-itineraries.index', compact('itineraries', 'destinations'));
    }

    public function create(Request $request)
    {
        $destinations = \App\Models\Country::orderBy('name')->get();
        $admins = \App\Models\Admin::orderBy('name')->get();
        return view('admin.group-itineraries.create', compact('destinations', 'admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|exists:countries,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
        ]);

        $itineraryStructure = ItineraryHelper::generateSampleItinerary($request->duration_days, $request->destination_id);

        $itinerary = GroupItinerary::create([
            'user_id' => $request->user_id ?? auth()->id(),
            'destination_id' => $request->destination_id,
            'country_ids' => $request->country_ids ?? [],
            'title' => $request->title,
            'client_name' => $request->client_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'start_date' => $request->start_date,
            'duration_days' => $request->duration_days,
            'itinerary' => $itineraryStructure,
            'status' => 'draft',
        ]);

        $itinerary->calculatePricing()->save();

        return redirect()->route('admin.group-itineraries.edit', $itinerary->id)
            ->with('success', 'Group itinerary draft created. Now build it.');
    }

    public function edit($id)
    {
        $itinerary = GroupItinerary::with(['destination'])->findOrFail($id);
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = \App\Models\Destination::orderBy('city')->get();
        $admins = \App\Models\Admin::orderBy('name')->get();
        return view('admin.group-itineraries.edit', compact('itinerary', 'countries', 'destinations', 'admins'));
    }

    public function update(Request $request, $id)
    {
        $itinerary = GroupItinerary::findOrFail($id);

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
            'country_ids' => $request->country_ids ?? $itinerary->country_ids,
            'email' => $request->email,
            'phone' => $request->phone,
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
                        'itinerary_type' => 'group',
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
                    ->where('itinerary_type', 'group')
                    ->where('category', 'Involved Vendor')
                    ->where('amount', 0)
                    ->whereNotIn('supplier_id', $vendorIds)
                    ->delete();
            }
        }

        return redirect()->back()->with('success', 'Group Itinerary updated successfully.');
    }

    public function show($id)
    {
        $itinerary = GroupItinerary::with(['destination'])->findOrFail($id);
        $enrichedItinerary = ItineraryHelper::enrichItinerary($itinerary->itinerary);
        return view('admin.group-itineraries.show', compact('itinerary', 'enrichedItinerary'));
    }

    public function pdf($id)
    {
        $itinerary = GroupItinerary::with(['destination'])->findOrFail($id);
        $enrichedItinerary = ItineraryHelper::enrichItinerary($itinerary->itinerary);

        $data = [
            'itinerary' => $itinerary,
            'enrichedItinerary' => $enrichedItinerary,
            'client' => $itinerary->client_name,
            'generated_at' => now()->format('d M Y'),
            'agency' => (object) [
                'company_name' => config('tourliz.brand.name', 'Tourliz Official'),
                'whatsapp_number' => config('tourliz.brand.whatsapp', '+60 12-345 6789'),
                'logo' => file_exists(public_path(config('tourliz.brand.logo_path', 'img/tourliz_logo.png'))) ? asset(config('tourliz.brand.logo_path', 'img/tourliz_logo.png')) : null,
            ],
            'is_public' => request()->has('public') || request()->get('mode') === 'customer'
        ];

        // Using b2b.pdf as a common template if it works, or we can create a group-specific one
        $pdf = Pdf::loadView('admin.b2b.pdf', $data);

        $filename = ($data['is_public'] ? 'Group_Proposal' : 'Group_Internal') . '_' .
            \Illuminate\Support\Str::slug(ucwords(strtolower($itinerary->client_name ?? 'Group'))) . '_' .
            now()->format('d_M_Y') . '.pdf';

        return $pdf->download($filename);
    }

    public function whatsapp($id)
    {
        $itinerary = GroupItinerary::with(['destination'])->findOrFail($id);
        $data = $itinerary->itinerary;

        $text = "*GROUP PROPOSAL: " . strtoupper($itinerary->title) . "*\n";
        $text .= "Ref ID: " . $itinerary->quote_id . "\n";
        $text .= "Destination: " . ($itinerary->destination->name ?? 'N/A') . "\n";
        $text .= "Duration: " . $itinerary->duration_days . " Days\n";
        $text .= "Group/Lead: " . ($itinerary->client_name ?? 'Valued Group') . "\n";
        $text .= "Pax: " . ($itinerary->adults ?: 1) . " Adults";
        if ($itinerary->children_2_6 > 0)
            $text .= ", " . $itinerary->children_2_6 . " Child (2-6y)";
        if ($itinerary->children_6_11 > 0)
            $text .= ", " . $itinerary->children_6_11 . " Child (6-11y)";
        $text .= "\n\n";

        foreach ($data as $day) {
            $text .= "*Day " . ($day['day'] ?? '?') . ": " . ($day['title'] ?? 'Untitled') . "*\n";
            // ... (rest of the logic same as b2c)
        }

        $text .= "*Total Final Quote:* " . $itinerary->currency . " " . number_format($itinerary->total_price, 2) . "\n";

        $adults = (int) $itinerary->adults;
        $c1 = (int) $itinerary->children_2_6;
        $c2 = (int) $itinerary->children_6_11;
        $hasChildren = ($c1 > 0 || $c2 > 0);

        if ($hasChildren) {
            $weightedPax = ($adults * 1.0) + ($c1 * 0.25) + ($c2 * 0.50);
            $adultCost = $weightedPax > 0 ? ($itinerary->total_price / $weightedPax) : 0;
            $child26Cost = $adultCost * 0.25;
            $child611Cost = $adultCost * 0.50;

            $text .= "--- PRICING BREAKDOWN ---\n";
            $text .= "• Adult: " . $itinerary->currency . " " . number_format($adultCost, 2) . " x " . $adults . " = " . $itinerary->currency . " " . number_format($adultCost * $adults, 2) . "\n";
            if ($c1 > 0) {
                $text .= "• Child (2-6y): " . $itinerary->currency . " " . number_format($child26Cost, 2) . " x " . $c1 . " = " . $itinerary->currency . " " . number_format($child26Cost * $c1, 2) . "\n";
            }
            if ($c2 > 0) {
                $text .= "• Child (6-11y): " . $itinerary->currency . " " . number_format($child611Cost, 2) . " x " . $c2 . " = " . $itinerary->currency . " " . number_format($child611Cost * $c2, 2) . "\n";
            }
            $text .= "\n";
        }

        $text .= "Payment Status: " . strtoupper($itinerary->payment_status) . "\n\n";
        $text .= "Thank you for choosing Tourliz!";

        return response()->json(['text' => $text]);
    }

    public function destroy($id)
    {
        GroupItinerary::findOrFail($id)->delete();
        return redirect()->route('admin.group-itineraries.index')->with('success', 'Group itinerary deleted.');
    }
}
