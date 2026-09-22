<?php

namespace App\Http\Controllers\Admin\Itinerary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\B2CItineraryRequest;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Services\ItineraryService;
use Illuminate\Http\Request;

class B2CItineraryController extends Controller
{
    protected $itineraryService;

    public function __construct(ItineraryService $itineraryService)
    {
        $this->itineraryService = $itineraryService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'destination_id' => $request->get('destination_id'),
            'status' => $request->get('status'),
            'is_published' => $request->get('is_published'),
        ];

        $itineraries = $this->itineraryService->getPaginatedItineraries('b2c', $filters);
        $destinations = Destination::orderBy('name')->get();

        return view('admin.itineraries.b2c.index', compact('itineraries', 'destinations', 'filters'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        return view('admin.itineraries.b2c.create', compact('destinations'));
    }

    public function store(B2CItineraryRequest $request)
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('itineraries/b2c', 'public');
        }

        $itinerary = $this->itineraryService->createB2CItinerary($request->validated(), $imagePath);

        return redirect()->route('admin.itineraries.b2c.index')
            ->with('success', "B2C Itinerary '{$itinerary->title}' created successfully.");
    }

    public function show($id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2c')
            ->with(['destination', 'days.items', 'b2cDetail'])
            ->findOrFail($id);

        return view('admin.itineraries.b2c.show', compact('itinerary'));
    }

    public function edit($id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2c')
            ->with(['destination', 'days.items', 'b2cDetail'])
            ->findOrFail($id);

        $destinations = Destination::orderBy('name')->get();

        return view('admin.itineraries.b2c.edit', compact('itinerary', 'destinations'));
    }

    public function update(B2CItineraryRequest $request, $id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2c')->findOrFail($id);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('itineraries/b2c', 'public');
        }

        $this->itineraryService->updateB2CItinerary($itinerary, $request->validated(), $imagePath);

        return redirect()->route('admin.itineraries.b2c.index')
            ->with('success', "B2C Itinerary '{$itinerary->title}' updated successfully.");
    }

    public function destroy($id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2c')->findOrFail($id);
        $title = $itinerary->title;
        $itinerary->delete();

        return redirect()->route('admin.itineraries.b2c.index')
            ->with('success', "B2C Itinerary '{$title}' deleted successfully.");
    }
}
