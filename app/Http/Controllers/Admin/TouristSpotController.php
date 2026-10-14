<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristSpot;
use App\Models\Destination;
use App\Models\Country;
use App\Models\Supplier;
use Illuminate\Http\Request;

class TouristSpotController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page'), [15, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 15;

        $spots = TouristSpot::query()
            ->with(['country', 'destination'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('country_id'), fn ($query) =>
                $query->where('country_id', $request->input('country_id')))
            ->when($request->filled('destination_id'), fn ($query) =>
                $query->where('destination_id', $request->input('destination_id')))
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), fn ($query) =>
                $query->where('is_active', $request->input('status') === 'active'))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $countries = Country::orderBy('name')->get(['id', 'name']);
        $destinations = Destination::orderBy('name')->get(['id', 'name']);

        return view('admin.tourist-spots.index', compact('spots', 'countries', 'destinations'));
    }

    public function create()
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = Destination::all();
        $suppliers = Supplier::where('is_active', 1)->orderBy('name')->get();
        return view('admin.tourist-spots.create', compact('countries', 'destinations', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'destination_id' => 'nullable|exists:destinations,id',
        ]);

        TouristSpot::create($request->all());

        return redirect()->route('admin.tourist-spots.index')->with('success', 'Tourist spot created.');
    }

    public function edit(TouristSpot $touristSpot)
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = Destination::all();
        $suppliers = Supplier::where('is_active', 1)->orderBy('name')->get();
        return view('admin.tourist-spots.edit', compact('touristSpot', 'countries', 'destinations', 'suppliers'));
    }

    public function update(Request $request, TouristSpot $touristSpot)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'destination_id' => 'nullable|exists:destinations,id',
        ]);

        $touristSpot->update($request->all());

        return redirect()->route('admin.tourist-spots.index')->with('success', 'Tourist spot updated.');
    }

    public function destroy(TouristSpot $touristSpot)
    {
        $touristSpot->delete();
        return redirect()->route('admin.tourist-spots.index')->with('success', 'Tourist spot deleted.');
    }
}
