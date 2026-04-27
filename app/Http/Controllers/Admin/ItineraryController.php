<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Destination;
use App\Models\Attraction;
use App\Helpers\ItineraryHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ItineraryController extends Controller
{
    /**
     * Display itinerary management page
     */
    public function index()
    {
        $packages = Package::with('destination')
            ->whereNotNull('itinerary')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('admin.itineraries.index', compact('packages'));
    }

    /**
     * Show itinerary builder for a package
     */
    public function edit($id)
    {
        $package = Package::with(['destination', 'destination.countryRel'])->findOrFail($id);
        $destinations = Destination::orderBy('name')->get();
        $attractions = Attraction::orderBy('name')->get();
        $countryName = $package->destination->country ?? null;
        
        $suppliersQuery = \App\Models\Supplier::query();
        $touristSpotsQuery = \App\Models\TouristSpot::query();
        $coreServicesQuery = \App\Models\Service::query();
        
        if ($countryName || $package->destination_id) {
            $filter = function($q) use ($countryName, $package) {
                if ($countryName) {
                    $q->whereHas('destination', function($sq) use ($countryName) {
                        $sq->where('country', $countryName);
                    });
                }
                if ($package->destination_id) {
                    $q->orWhere('destination_id', $package->destination_id);
                }
            };
            $suppliersQuery->where($filter);
            $touristSpotsQuery->where($filter);
            $coreServicesQuery->where($filter);
        }
        
        $suppliers = $suppliersQuery->orderBy('name')->get();
        $touristSpots = $touristSpotsQuery->orderBy('name')->get();
        $coreServices = $coreServicesQuery->orderBy('name')->get();

        // Fallbacks if empty
        if ($suppliers->count() === 0) $suppliers = \App\Models\Supplier::orderBy('name')->take(50)->get();
        if ($touristSpots->count() === 0) $touristSpots = \App\Models\TouristSpot::orderBy('name')->take(50)->get();
        if ($coreServices->count() === 0) $coreServices = \App\Models\Service::orderBy('name')->take(50)->get();

        // Initialize empty itinerary if doesn't exist
        if (!$package->itinerary) {
            $package->itinerary = [];
        }

        return view('admin.itineraries.edit', compact('package', 'destinations', 'attractions', 'touristSpots', 'coreServices', 'suppliers'));
    }

    /**
     * Update itinerary
     */
    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $request->validate([
            'itinerary' => 'required|json',
        ]);

        $itinerary = json_decode($request->itinerary, true);

        // Validate itinerary structure
        $validation = ItineraryHelper::validateItinerary($itinerary);
        if (!$validation['valid']) {
            return back()->withErrors(['itinerary' => $validation['errors']]);
        }

        $package->itinerary = $itinerary;
        $package->save();

        return redirect()->route('admin.itineraries.edit', $id)
            ->with('success', 'Itinerary updated successfully!');
    }

    /**
     * Generate sample itinerary
     */
    public function generate($id)
    {
        $package = Package::findOrFail($id);

        // Determine number of days
        $days = 3;
        if ($package->duration && preg_match('/(\d+)\s*days?/i', $package->duration, $matches)) {
            $days = (int) $matches[1];
        }

        $itinerary = ItineraryHelper::generateSampleItinerary($days, $package->destination_id);
        $package->itinerary = $itinerary;
        $package->save();

        return redirect()->route('admin.itineraries.edit', $id)
            ->with('success', "Generated {$days}-day sample itinerary!");
    }

    /**
     * Delete itinerary
     */
    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->itinerary = null;
        $package->save();

        return redirect()->route('admin.itineraries.index')
            ->with('success', 'Itinerary deleted successfully!');
    }

    /**
     * Preview itinerary
     */
    public function preview($id)
    {
        $package = Package::with('destination')->findOrFail($id);

        if (!$package->hasItinerary()) {
            return back()->with('error', 'This package does not have an itinerary.');
        }

        $enrichedItinerary = ItineraryHelper::enrichItinerary(
            $this->sanitizeItinerary($package->itinerary)
        );
        $costBreakdown = ItineraryHelper::calculateTotalCost($enrichedItinerary, $package->currency);

        return view('admin.itineraries.preview', compact('package', 'enrichedItinerary', 'costBreakdown'));
    }

    /**
     * Export itinerary as PDF
     */
    public function exportPdf($id)
    {
        $package = Package::with('destination')->findOrFail($id);

        if (!$package->hasItinerary()) {
            return back()->with('error', 'This package does not have an itinerary.');
        }

        $enrichedItinerary = ItineraryHelper::enrichItinerary(
            $this->sanitizeItinerary($package->itinerary)
        );
        $costBreakdown = ItineraryHelper::calculateTotalCost($enrichedItinerary, $package->currency);

        $pdf = Pdf::loadView('admin.itineraries.pdf', compact('package', 'enrichedItinerary', 'costBreakdown'));

        $filename = 'Itinerary_' . str_replace(' ', '-', strtolower($package->name)) . '_' . now()->format('d_M_Y') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Sanitize/normalize any legacy string-format fields in an itinerary
     * so that Blade templates can always use array access safely.
     */
    private function sanitizeItinerary(array $itinerary): array
    {
        return array_map(function (array $day) {
            // Normalize hotel: string → array
            $hotel = $day['hotel'] ?? null;
            if (is_string($hotel)) {
                $day['hotel'] = ['name' => $hotel, 'type' => '', 'price_per_night' => 0, 'currency' => 'MYR'];
            } elseif (!is_array($hotel)) {
                $day['hotel'] = ['name' => '', 'type' => '', 'price_per_night' => 0, 'currency' => 'MYR'];
            }

            // Normalize meals: indexed array ['Breakfast','Lunch'] → keyed object
            $meals = $day['meals'] ?? [];
            if (is_array($meals) && isset($meals[0])) {
                $day['meals'] = [
                    'breakfast' => in_array('Breakfast', $meals) ? 'Included' : 'Not included',
                    'lunch'     => in_array('Lunch',     $meals) ? 'Included' : 'Not included',
                    'dinner'    => in_array('Dinner',    $meals) ? 'Included' : 'Not included',
                ];
            } elseif (!is_array($meals)) {
                $day['meals'] = ['breakfast' => 'Not included', 'lunch' => 'Not included', 'dinner' => 'Not included'];
            }

            // Normalize transport: string → array of objects
            $transport = $day['transport'] ?? [];
            if (is_string($transport) && !empty($transport)) {
                $day['transport'] = [['type' => 'Local Transport', 'mode' => $transport, 'from' => '', 'to' => '', 'price' => 0]];
            } elseif (!is_array($transport)) {
                $day['transport'] = [];
            } else {
                $day['transport'] = array_map(function ($t) {
                    if (is_string($t)) {
                        return ['type' => 'Transport', 'mode' => $t, 'from' => '', 'to' => '', 'price' => 0, 'currency' => 'MYR'];
                    }
                    if (is_array($t)) {
                        // If it's a generic type but has mode, maybe prefer mode
                        if (isset($t['type']) && in_array(strtolower($t['type']), ['component', 'custom'])) {
                            $t['type'] = $t['mode'] ?? $t['type'];
                        }
                        return $t;
                    }
                    return ['type' => 'Transport', 'mode' => '', 'from' => '', 'to' => '', 'price' => 0];
                }, $day['transport']);
            }

            // Normalize activities: ensure it's an array of arrays
            if (!isset($day['activities']) || !is_array($day['activities'])) {
                $day['activities'] = [];
            } else {
                $day['activities'] = array_map(function ($activity) {
                    return is_string($activity) ? ['name' => $activity] : $activity;
                }, $day['activities']);
            }
            
            // Normalize places: ensure it's an array of arrays
            if (!isset($day['places']) || !is_array($day['places'])) {
                $day['places'] = [];
            } else {
                $day['places'] = array_map(function ($place) {
                    return is_string($place) ? ['name' => $place] : $place;
                }, $day['places']);
            }

            return $day;
        }, $itinerary);
    }

    /**
     * Add a new day to itinerary
     */
    public function addDay(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $itinerary = $package->itinerary ?? [];
        $dayNumber = count($itinerary) + 1;

        $newDay = [
            'day' => $dayNumber,
            'title' => $request->input('title', 'Day ' . $dayNumber),
            'places' => [],
            'hotel' => null,
            'transport' => [],
            'activities' => [],
            'meals' => [
                'breakfast' => 'Not included',
                'lunch' => 'Not included',
                'dinner' => 'Not included'
            ],
            'notes' => ''
        ];

        $itinerary[] = $newDay;
        $package->itinerary = $itinerary;
        $package->save();

        return response()->json([
            'success' => true,
            'message' => 'Day added successfully',
            'day' => $newDay
        ]);
    }

    /**
     * Remove a day from itinerary
     */
    public function removeDay(Request $request, $id, $dayIndex)
    {
        $package = Package::findOrFail($id);

        $itinerary = $package->itinerary ?? [];

        if (isset($itinerary[$dayIndex])) {
            array_splice($itinerary, $dayIndex, 1);

            // Re-number days
            foreach ($itinerary as $index => &$day) {
                $day['day'] = $index + 1;
            }

            $package->itinerary = $itinerary;
            $package->save();

            return response()->json([
                'success' => true,
                'message' => 'Day removed successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Day not found'
        ], 404);
    }
}
