<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristSpot;
use App\Models\Destination;
use App\Models\Supplier;
use Illuminate\Http\Request;

class TouristSpotController extends Controller
{
    public function index()
    {
        $spots = TouristSpot::with(['country', 'destination'])->paginate(15);
        return view('admin.tourist-spots.index', compact('spots'));
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
            'country_id' => 'nullable|exists:countries,id',
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
            'country_id' => 'nullable|exists:countries,id',
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
