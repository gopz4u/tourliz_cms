<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Destination;
use App\Models\Supplier;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::with('destination', 'rooms')->paginate(15);
        return view('admin.hotels.index', compact('hotels'));
    }

    public function create()
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = Destination::all();
        $suppliers = Supplier::where('type', 'Hotel')->where('is_active', 1)->orderBy('name')->get();
        return view('admin.hotels.create', compact('countries', 'destinations', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'star_rating' => 'required|integer|min:1|max:5',
        ]);

        $hotel = Hotel::create($request->all());

        if ($request->has('rooms')) {
            foreach ($request->rooms as $room) {
                if ($room['room_type']) {
                    $hotel->rooms()->create($room);
                }
            }
        }

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel created successfully.');
    }

    public function edit(Hotel $hotel)
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = Destination::all();
        $hotel->load('rooms');
        $suppliers = Supplier::where('type', 'Hotel')->where('is_active', 1)->orderBy('name')->get();
        return view('admin.hotels.edit', compact('hotel', 'countries', 'destinations', 'suppliers'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
        ]);

        $hotel->update($request->all());

        // Simple sync for rooms
        $hotel->rooms()->delete();
        if ($request->has('rooms')) {
            foreach ($request->rooms as $room) {
                if ($room['room_type']) {
                    $hotel->rooms()->create($room);
                }
            }
        }

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel updated successfully.');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return redirect()->route('admin.hotels.index')->with('success', 'Hotel deleted successfully.');
    }
}
