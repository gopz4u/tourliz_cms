<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FixedItinerary;
use Illuminate\Http\Request;

class FixedItineraryController extends Controller
{
    public function index(Request $request)
    {
        $query = FixedItinerary::with(['supplier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $itineraries = $query->orderBy('updated_at', 'desc')->paginate(15);
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.fixed-itineraries.index', compact('itineraries', 'suppliers'));
    }

    public function create()
    {
        $countries = \App\Models\Country::where('status', true)->orderBy('name')->get();
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();
        return view('admin.fixed-itineraries.create', compact('countries', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'itinerary_description' => 'nullable|string',
            'fixed_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string',
        ]);

        FixedItinerary::create([
            'user_id' => auth()->id(),
            'supplier_id' => $validated['supplier_id'] ?? null,
            'country_ids' => $validated['country_ids'] ?? [],
            'title' => $validated['title'],
            'itinerary_description' => $validated['itinerary_description'] ?? null,
            'fixed_price' => $validated['fixed_price'],
            'currency' => $validated['currency'] ?? 'MYR',
            'status' => $validated['status'] ?? 'active',
        ]);

        return redirect()->route('admin.fixed-itineraries.index')
            ->with('success', 'Fixed itinerary created successfully.');
    }

    public function edit($id)
    {
        $itinerary = FixedItinerary::findOrFail($id);
        $countries = \App\Models\Country::where('status', true)->orderBy('name')->get();
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();
        return view('admin.fixed-itineraries.edit', compact('itinerary', 'countries', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $itinerary = FixedItinerary::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'itinerary_description' => 'nullable|string',
            'fixed_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string',
        ]);

        $itinerary->update([
            'supplier_id' => $validated['supplier_id'] ?? $itinerary->supplier_id,
            'country_ids' => $validated['country_ids'] ?? $itinerary->country_ids,
            'title' => $validated['title'],
            'itinerary_description' => $validated['itinerary_description'] ?? $itinerary->itinerary_description,
            'fixed_price' => $validated['fixed_price'],
            'currency' => $validated['currency'] ?? $itinerary->currency,
            'status' => $validated['status'] ?? $itinerary->status,
        ]);

        return redirect()->route('admin.fixed-itineraries.index')
            ->with('success', 'Fixed itinerary updated successfully.');
    }

    public function destroy($id)
    {
        FixedItinerary::findOrFail($id)->delete();
        return redirect()->route('admin.fixed-itineraries.index')
            ->with('success', 'Fixed itinerary deleted.');
    }
}
