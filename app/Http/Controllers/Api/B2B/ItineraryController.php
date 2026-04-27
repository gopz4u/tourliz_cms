<?php

namespace App\Http\Controllers\Api\B2B;

use App\Http\Controllers\Controller;
use App\Models\CustomItinerary;
use App\Models\Destination;
use App\Helpers\ItineraryHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ItineraryController extends Controller
{
    /**
     * Display a listing of custom itineraries.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = CustomItinerary::where('user_id', $user->id)
            ->with('destination')
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('client_name', 'like', '%' . $request->search . '%');
            });
        }

        $itineraries = $query->paginate(15);

        return response()->json($itineraries);
    }

    /**
     * Store a newly created itinerary.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'start_date' => 'nullable|date',
            'duration_days' => 'required|integer|min:1',
            'client_name' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Generate blank itinerary structure
        $itineraryStructure = ItineraryHelper::generateSampleItinerary($request->duration_days, $request->destination_id);

        // Clear sample content but keep structure
        foreach ($itineraryStructure as &$day) {
            $day['places'] = [];
            $day['hotel'] = null;
            $day['transport'] = [];
            $day['activities'] = [];
            $day['meals'] = [
                'breakfast' => 'Not included',
                'lunch' => 'Not included',
                'dinner' => 'Not included'
            ];
            $day['notes'] = '';
        }

        $customItinerary = new CustomItinerary([
            'user_id' => $user->id,
            'title' => $request->title,
            'client_name' => $request->client_name,
            'destination_id' => $request->destination_id,
            'start_date' => $request->start_date,
            'duration_days' => $request->duration_days,
            'itinerary' => $itineraryStructure,
            'status' => 'draft',
            'currency' => 'INR', // Default currency
        ]);

        $customItinerary->save();
        $customItinerary->calculatePricing();
        $customItinerary->save();

        return response()->json([
            'message' => 'Custom itinerary created successfully',
            'itinerary' => $customItinerary
        ], 201);
    }

    /**
     * Display the specified itinerary.
     */
    public function show($id)
    {
        $user = Auth::user();

        $itinerary = CustomItinerary::where('user_id', $user->id)
            ->with('destination')
            ->findOrFail($id);

        // Enrich itinerary data
        $itinerary->enriched_itinerary = ItineraryHelper::enrichItinerary($itinerary->itinerary);

        $pdfUrl = url("/api/b2b/itineraries/{$itinerary->id}/pdf");
        $message = urlencode("Check out this travel proposal for {$itinerary->title}: " . $pdfUrl);
        $itinerary->whatsapp_link = "https://wa.me/?text={$message}";

        return response()->json($itinerary);
    }

    /**
     * Update the specified itinerary.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $itinerary = CustomItinerary::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'title' => 'string|max:255',
            'client_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'status' => 'in:draft,proposed,confirmed,cancelled',
            'itinerary' => 'array',
            'markup_percentage' => 'numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $itinerary->fill($request->only([
            'title',
            'client_name',
            'start_date',
            'status',
            'itinerary',
            'markup_percentage',
            'notes'
        ]));

        if ($request->has('itinerary')) {
            // Recalculate duration based on itinerary array length
            if (is_array($request->itinerary)) {
                $itinerary->duration_days = count($request->itinerary);
            }

            // Validate structure
            $validation = ItineraryHelper::validateItinerary($request->itinerary);
            if (!$validation['valid']) {
                return response()->json(['errors' => $validation['errors']], 422);
            }
        }

        $itinerary->calculatePricing();
        $itinerary->save();

        return response()->json([
            'message' => 'Itinerary updated successfully',
            'itinerary' => $itinerary
        ]);
    }

    /**
     * Remove the specified itinerary.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $itinerary = CustomItinerary::where('user_id', $user->id)->findOrFail($id);
        $itinerary->delete();

        return response()->json([
            'message' => 'Itinerary deleted successfully'
        ]);
    }

    /**
     * Generate PDF Proposal
     */
    public function generatePdf($id)
    {
        $user = Auth::user();

        $customItinerary = CustomItinerary::where('user_id', $user->id)
            ->with('destination')
            ->findOrFail($id);

        $enrichedItinerary = ItineraryHelper::enrichItinerary($customItinerary->itinerary);

        // Prepare data for view
        $data = [
            'itinerary' => $customItinerary,
            'enrichedItinerary' => $enrichedItinerary,
            'agency' => $user,
            'client' => $customItinerary->client_name,
            'generated_at' => now()->format('Y-m-d'),
        ];

        $pdf = Pdf::loadView('api.b2b.pdf_proposal', $data);

        return $pdf->download('proposal-' . $customItinerary->id . '.pdf');
    }
}
