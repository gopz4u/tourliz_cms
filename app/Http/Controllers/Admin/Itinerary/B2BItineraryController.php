<?php

namespace App\Http\Controllers\Admin\Itinerary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\B2BItineraryRequest;
use App\Models\Agency;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Supplier;
use App\Services\ItineraryService;
use Illuminate\Http\Request;

class B2BItineraryController extends Controller
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

        $itineraries = $this->itineraryService->getPaginatedItineraries('b2b', $filters);
        $destinations = Destination::orderBy('name')->get();

        return view('admin.itineraries.b2b.index', compact('itineraries', 'destinations', 'filters'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        $agencies = Agency::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.itineraries.b2b.create', compact('destinations', 'agencies', 'suppliers'));
    }

    public function store(B2BItineraryRequest $request)
    {
        $itinerary = $this->itineraryService->createB2BItinerary($request->validated());

        return redirect()->route('admin.itineraries.b2b.index')
            ->with('success', "B2B Itinerary '{$itinerary->title}' created successfully.");
    }

    public function show($id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2b')
            ->with(['destination', 'days.items', 'b2bDetail.agency', 'b2bDetail.supplier'])
            ->findOrFail($id);

        return view('admin.itineraries.b2b.show', compact('itinerary'));
    }

    public function edit($id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2b')
            ->with(['destination', 'days.items', 'b2bDetail.agency', 'b2bDetail.supplier'])
            ->findOrFail($id);

        $destinations = Destination::orderBy('name')->get();
        $agencies = Agency::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.itineraries.b2b.edit', compact('itinerary', 'destinations', 'agencies', 'suppliers'));
    }

    public function update(B2BItineraryRequest $request, $id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2b')->findOrFail($id);

        $this->itineraryService->updateB2BItinerary($itinerary, $request->validated());

        return redirect()->route('admin.itineraries.b2b.index')
            ->with('success', "B2B Itinerary '{$itinerary->title}' updated successfully.");
    }

    public function destroy($id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2b')->findOrFail($id);
        $title = $itinerary->title;
        $itinerary->delete();

        return redirect()->route('admin.itineraries.b2b.index')
            ->with('success', "B2B Itinerary '{$title}' deleted successfully.");
    }
}
