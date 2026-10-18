<?php

namespace App\Http\Controllers\Admin\Itinerary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\B2CItineraryRequest;
use App\Models\B2CItinerary as LegacyB2CItinerary;
use App\Models\Destination;
use App\Models\FixedItinerary;
use App\Models\GroupPackage;
use App\Models\Itinerary;
use App\Services\ItineraryImportService;
use App\Services\ItineraryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class B2CItineraryController extends Controller
{
    protected $itineraryService;
    protected $itineraryImportService;

    public function __construct(ItineraryService $itineraryService, ItineraryImportService $itineraryImportService)
    {
        $this->itineraryService = $itineraryService;
        $this->itineraryImportService = $itineraryImportService;
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
        $countries = \App\Models\Country::orderBy('name')->get();
        $fixedItineraries = FixedItinerary::where('status', 'active')->orderBy('title')->get(['id', 'title', 'duration_days']);
        $groupPackages = GroupPackage::where('status', true)->orderBy('name')->get(['id', 'name', 'duration']);
        $legacyB2cItineraries = LegacyB2CItinerary::whereNotNull('itinerary')
            ->latest('updated_at')
            ->limit(100)
            ->get(['id', 'title', 'duration_days']);
        $unifiedB2cItineraries = Itinerary::where('itinerary_type', 'b2c')
            ->with('b2cDetail')
            ->latest('updated_at')
            ->limit(100)
            ->get(['id', 'title', 'duration_days', 'updated_at']);

        return view('admin.itineraries.b2c.create', compact(
            'destinations',
            'countries',
            'fixedItineraries',
            'groupPackages',
            'legacyB2cItineraries',
            'unifiedB2cItineraries'
        ));
    }

    /**
     * Copy a source into the normalized B2C structure, leaving the source untouched.
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'source_type' => ['required', Rule::in(['fixed', 'group', 'legacy_b2c', 'unified_b2c'])],
            'source_id' => ['required', 'integer', 'min:1'],
            'destination_id' => ['required', 'exists:destinations,id'],
            'as_quote' => ['nullable', 'boolean'],
        ]);

        $itinerary = $this->itineraryImportService->importToB2C(
            $validated['source_type'],
            (int) $validated['source_id'],
            (int) $validated['destination_id'],
            !$request->boolean('as_quote')
        );

        return redirect()->route('admin.itineraries.b2c.edit', $itinerary->id)
            ->with('success', 'Source imported into a new draft. Review it before publishing or sharing.');
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
        $countries = \App\Models\Country::orderBy('name')->get();

        return view('admin.itineraries.b2c.edit', compact('itinerary', 'destinations', 'countries'));
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

    public function generatePdf($id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2c')
            ->with(['destination', 'days.items', 'b2cDetail'])
            ->findOrFail($id);
            
        // We will generate a view first to see what we have
        // Later we can implement proper PDF generation using dompdf or browsershot
        return view('admin.itineraries.b2c.pdf', compact('itinerary'));
    }

    public function shareWhatsapp($id)
    {
        $itinerary = Itinerary::where('itinerary_type', 'b2c')
            ->with(['b2cDetail'])
            ->findOrFail($id);
            
        $name = $itinerary->b2cDetail->customer_name ?? 'Customer';
        $title = $itinerary->title;
        $price = $itinerary->b2cDetail->selling_price ?? 0;
        
        $message = "Hello {$name},\n\n";
        $message .= "Here is your quotation for *{$title}*.\n\n";
        $message .= "Total Package Price: RM " . number_format($price, 2) . "\n\n";
        $message .= "View full itinerary details here: " . route('quote.show', $itinerary->slug) . "\n\n";
        $message .= "Please let us know if you have any questions!\n\n";
        $message .= "Best regards,\nTourliz Holidays";

        $phone = preg_replace('/[^0-9]/', '', $itinerary->b2cDetail->customer_whatsapp);
        $nativeUrl = "whatsapp://send?phone={$phone}&text=" . rawurlencode($message);

        return view('admin.whatsapp-launcher', compact('nativeUrl'));
    }
}
