<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Supplier::orderBy('name');
            
            if ($request->filled('country_id') || $request->filled('destination_id')) {
                $destId = $request->country_id ?? $request->destination_id;
                
                // Try to find if it's a City or Country
                $destination = \App\Models\Destination::find($destId);
                $country = \App\Models\Country::find($destId);
                
                $query->where(function($q) use ($destId, $destination, $country) {
                    $q->where('destination_id', $destId)
                      ->orWhereNull('destination_id'); // Include general vendors
                    
                    if ($destination && $destination->country) {
                        $q->orWhereHas('destination', function($sq) use ($destination) {
                            $sq->where('country', $destination->country);
                        });
                    }
                    
                    if ($country) {
                        $q->orWhereHas('destination', function($sq) use ($country) {
                            $sq->where('country', $country->name);
                        });
                    }
                });
            }
            
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            
            $results = $query->get();
            
            // Ultimate fallback: if filtered list is empty, return all active suppliers
            if ($results->isEmpty()) {
                $results = Supplier::orderBy('name')->take(50)->get();
            }
            
            return response()->json($results);
        }
        $filterType = $request->input('type');
        return view('admin.suppliers.index', compact('filterType'));
    }

    public function show(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'type' => 'required|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'swift_ifsc' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $supplier = Supplier::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'supplier' => $supplier]);
        }

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'type' => 'required|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'swift_ifsc' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $supplier->update($validated);

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): \Illuminate\Http\JsonResponse
    {
        $supplier->delete();
        return response()->json(['success' => true]);
    }
}
