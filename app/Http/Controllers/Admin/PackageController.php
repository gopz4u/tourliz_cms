<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            try {
                $query = Package::withTrashed()->with([
                    'destination' => function ($q) {
                        $q->withTrashed(); // Include soft-deleted destinations
                    }
                ]);

                if ($request->has('search')) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                }

                // Filter by destination if provided
                if ($request->has('destination_id') && $request->destination_id) {
                    $query->where('destination_id', $request->destination_id);
                }

                $packages = $query->orderBy('created_at', 'desc')->paginate(15);

                return response()->json($packages);
            } catch (\Exception $e) {
                \Log::error('Error loading packages: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Error loading packages',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        return view('admin.packages.index');
    }

    /**
     * Get packages by place
     */
    public function getByPlace($destinationId)
    {
        $packages = Package::where('destination_id', $destinationId)
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($packages);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();
        return view('admin.packages.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'destination_id' => 'nullable|exists:destinations,id',
            'destination_ids' => 'nullable|array',
            'supplier_ids' => 'nullable|array',
            'categories' => 'nullable|array',
            'package_category' => 'nullable|string|in:Honeymoon,Budget,Standard,Premium,Platinum',
            'includes_flight' => 'nullable',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'vehicle_type' => 'nullable|string|max:255',
            'accommodation_type' => 'nullable|string|max:255',
            'ticket_count' => 'nullable|integer|min:1',
            'ticket_name' => 'nullable|string|max:255',
            'addon_amenities' => 'nullable|array',
            'addon_amenities.*.type' => 'required|string',
            'addon_amenities.*.name' => 'required|string|max:255',
            'addon_amenities.*.value' => 'nullable|string|max:255',
            'addon_amenities.*.supplier_id' => 'nullable',
            'addon_amenities.*.asset_id' => 'nullable',
            'addon_amenities.*.price' => 'nullable|numeric|min:0',
            'addon_amenities.*.adult_price' => 'nullable|numeric|min:0',
            'addon_amenities.*.child_price' => 'nullable|numeric|min:0',
            'addon_amenities.*.quantity' => 'nullable|numeric|min:0',
            'addon_amenities.*.days' => 'nullable|numeric|min:0',
            'addon_amenities.*.adult_count' => 'nullable|numeric|min:0',
            'addon_amenities.*.child_count' => 'nullable|numeric|min:0',
            'addon_amenities.*.total' => 'nullable|numeric|min:0',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packages,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'price_2_6' => 'nullable|numeric|min:0',
            'price_6_10' => 'nullable|numeric|min:0',
            'announcement_date' => 'nullable|date',
            'total_pax' => 'nullable|integer|min:1',
            'duration_days' => 'nullable|integer|min:0',
            'duration_nights' => 'nullable|integer|min:0',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'inclusions' => 'nullable|array',
            'exclusions' => 'nullable|array',
            'itinerary' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Map form fields to database columns
        $packageData = [
            'destination_id' => $validated['destination_id'] ?? null,
            'destination_ids' => $validated['destination_ids'] ?? [],
            'supplier_ids' => $validated['supplier_ids'] ?? [],
            'supplier_id' => isset($validated['supplier_ids']) && count($validated['supplier_ids']) > 0 ? $validated['supplier_ids'][0] : null,
            'categories' => $validated['categories'] ?? [],
            'category' => isset($validated['categories']) && count($validated['categories']) > 0 ? $validated['categories'][0] : null,
            'package_category' => $validated['package_category'] ?? null,
            'star_rating' => $validated['star_rating'] ?? null,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'accommodation_type' => $validated['accommodation_type'] ?? null,
            'ticket_count' => $validated['ticket_count'] ?? null,
            'ticket_name' => $validated['ticket_name'] ?? null,
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'price_2_6' => $validated['price_2_6'] ?? null,
            'price_6_10' => $validated['price_6_10'] ?? null,
            'currency' => $validated['currency'] ?? 'MYR',
            'duration' => $this->formatDuration($validated['duration_days'] ?? null, $validated['duration_nights'] ?? null),
            'announcement_date' => $validated['announcement_date'] ?? null,
            'total_pax' => $validated['total_pax'] ?? null,
            'image' => $validated['image'] ?? null,
            'gallery' => $validated['gallery'] ?? [],
            'addon_amenities' => $validated['addon_amenities'] ?? [],
            'included_services' => $validated['inclusions'] ?? [],
            'excluded_services' => $validated['exclusions'] ?? [],
            'itinerary' => $this->normalizeItinerary($validated['itinerary'] ?? []),
            'featured' => isset($validated['is_featured']) ? (bool) $validated['is_featured'] : false,
            'status' => isset($validated['is_active']) ? (bool) $validated['is_active'] : true,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
        ];

        $package = Package::create($packageData);

        return response()->json($package, 201);
    }

    /**
     * Normalize itinerary array from the create/edit form into the
     * rich structure expected by the Manage Itinerary editor.
     */
    private function normalizeItinerary(array $itinerary): array
    {
        return array_values(array_map(function ($day) {
            // Normalize meals: array ['Breakfast','Lunch'] → object {breakfast:..., lunch:..., dinner:...}
            $mealsInput = $day['meals'] ?? [];
            if (is_array($mealsInput) && isset($mealsInput[0])) {
                // Came from multi-select (indexed array of strings)
                $meals = [
                    'breakfast' => in_array('Breakfast', $mealsInput) ? 'Included' : 'Not included',
                    'lunch'     => in_array('Lunch', $mealsInput)     ? 'Included' : 'Not included',
                    'dinner'    => in_array('Dinner', $mealsInput)    ? 'Included' : 'Not included',
                ];
            } elseif (is_array($mealsInput) && array_key_exists('breakfast', $mealsInput)) {
                // Already in object form (from manage-itinerary editor re-saving)
                $meals = $mealsInput;
            } else {
                $meals = ['breakfast' => 'Not included', 'lunch' => 'Not included', 'dinner' => 'Not included'];
            }

            // Normalize hotel: "hotel_18" (string) or null → object
            $hotelInput = $day['hotel'] ?? null;
            if (is_string($hotelInput) && !empty($hotelInput)) {
                // e.g. "hotel_18" — just keep the reference as a name for now
                $hotel = ['name' => $hotelInput, 'type' => '', 'price_per_night' => 0, 'currency' => 'MYR'];
            } elseif (is_array($hotelInput)) {
                $hotel = $hotelInput;
            } else {
                $hotel = ['name' => '', 'type' => '', 'price_per_night' => 0, 'currency' => 'MYR'];
            }

            // Custom hotel from the "+ Custom" button
            if (!empty($day['custom_hotel'])) {
                $hotel['name'] = $day['custom_hotel'];
            }

            // Normalize transport: string or null → array of transport objects
            $transportInput = $day['transport'] ?? ($day['transport_id'] ?? null);
            if (is_string($transportInput) && !empty($transportInput)) {
                $transport = [['type' => 'Local Transport', 'mode' => $transportInput, 'from' => '', 'to' => '', 'price' => 0]];
            } elseif (is_array($transportInput) && isset($transportInput[0]) && is_array($transportInput[0])) {
                $transport = $transportInput; // already array of objects
            } elseif (!empty($day['custom_transport'])) {
                $transport = [['type' => 'Local Transport', 'mode' => $day['custom_transport'], 'from' => '', 'to' => '', 'price' => 0]];
            } else {
                $transport = [];
            }

            // Normalize activities: ensure it is an array of objects
            $activities = $day['activities'] ?? [];
            if (is_array($activities)) {
                $activities = array_map(function ($act) {
                    return is_string($act) ? ['name' => $act] : $act;
                }, $activities);
            } else {
                $activities = [];
            }

            // Normalize places: ensure it is an array of objects
            $places = $day['places'] ?? [];
            if (is_array($places)) {
                $places = array_map(function ($place) {
                    return is_string($place) ? ['name' => $place] : $place;
                }, $places);
            } else {
                $places = [];
            }

            return [
                'day'         => (int) ($day['day'] ?? 1),
                'title'       => $day['title'] ?? ('Day ' . ($day['day'] ?? 1)),
                'places'      => $places,
                'activities'  => $activities,
                'transport'   => $transport,
                'hotel'       => $hotel,
                'meals'       => $meals,
                'notes'       => $day['description'] ?? ($day['notes'] ?? ''),
                'destinations'=> $day['destinations'] ?? [],
            ];
        }, $itinerary));
    }


    /**
     * Format duration from days and nights
     */
    private function formatDuration($days, $nights)
    {
        if (!$days && !$nights) {
            return null;
        }

        $parts = [];
        if ($days) {
            $parts[] = $days . ' ' . ($days == 1 ? 'day' : 'days');
        }
        if ($nights) {
            $parts[] = $nights . ' ' . ($nights == 1 ? 'night' : 'nights');
        }

        return implode(', ', $parts);
    }
    /**
     * Display the specified resource.
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $package = Package::withTrashed()->with([
                'destination' => function ($q) {
                    $q->withTrashed();
                }
            ])->findOrFail($id);
            return response()->json($package);
        } catch (\Exception $e) {
            \Log::error('Error loading package: ' . $e->getMessage());
            return response()->json([
                'error' => 'Package not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();
        return view('admin.packages.edit', ['id' => $id, 'suppliers' => $suppliers]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $package = Package::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'destination_id' => 'nullable|exists:destinations,id',
            'destination_ids' => 'nullable|array',
            'supplier_ids' => 'nullable|array',
            'categories' => 'nullable|array',
            'package_category' => 'nullable|string|in:Honeymoon,Budget,Standard,Premium,Platinum',
            'includes_flight' => 'nullable',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'vehicle_type' => 'nullable|string|max:255',
            'accommodation_type' => 'nullable|string|max:255',
            'ticket_count' => 'nullable|integer|min:1',
            'ticket_name' => 'nullable|string|max:255',
            'addon_amenities' => 'nullable|array',
            'addon_amenities.*.type' => 'required|string',
            'addon_amenities.*.name' => 'required|string|max:255',
            'addon_amenities.*.value' => 'nullable|string|max:255',
            'addon_amenities.*.supplier_id' => 'nullable',
            'addon_amenities.*.asset_id' => 'nullable',
            'addon_amenities.*.price' => 'nullable|numeric|min:0',
            'addon_amenities.*.adult_price' => 'nullable|numeric|min:0',
            'addon_amenities.*.child_price' => 'nullable|numeric|min:0',
            'addon_amenities.*.quantity' => 'nullable|numeric|min:0',
            'addon_amenities.*.days' => 'nullable|numeric|min:0',
            'addon_amenities.*.adult_count' => 'nullable|numeric|min:0',
            'addon_amenities.*.child_count' => 'nullable|numeric|min:0',
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('packages')->ignore($package->id)],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'price_2_6' => 'nullable|numeric|min:0',
            'price_6_10' => 'nullable|numeric|min:0',
            'announcement_date' => 'nullable|date',
            'total_pax' => 'nullable|integer|min:1',
            'currency' => 'nullable|string|in:INR,USD,MYR,SGD,AED',
            'duration_days' => 'nullable|integer|min:0',
            'duration_nights' => 'nullable|integer|min:0',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'inclusions' => 'nullable|array',
            'exclusions' => 'nullable|array',
            'itinerary' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // Handle boolean fields
        $validated['includes_flight'] = isset($validated['includes_flight']) && ($validated['includes_flight'] == 1 || $validated['includes_flight'] === true || $validated['includes_flight'] === '1' || $validated['includes_flight'] === 'true');

        // Map form fields to database columns
        $packageData = [
            'destination_id' => $validated['destination_id'] ?? null,
            'destination_ids' => $validated['destination_ids'] ?? [],
            'supplier_ids' => $validated['supplier_ids'] ?? [],
            'supplier_id' => isset($validated['supplier_ids']) && count($validated['supplier_ids']) > 0 ? $validated['supplier_ids'][0] : null,
            'categories' => $validated['categories'] ?? [],
            'category' => isset($validated['categories']) && count($validated['categories']) > 0 ? $validated['categories'][0] : null,
            'package_category' => $validated['package_category'] ?? null,
            'includes_flight' => $validated['includes_flight'] ?? false,
            'star_rating' => $validated['star_rating'] ?? null,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'accommodation_type' => $validated['accommodation_type'] ?? null,
            'ticket_count' => $validated['ticket_count'] ?? null,
            'ticket_name' => $validated['ticket_name'] ?? null,
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $package->slug,
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'price_2_6' => $validated['price_2_6'] ?? null,
            'price_6_10' => $validated['price_6_10'] ?? null,
            'currency' => $validated['currency'] ?? 'MYR',
            'duration' => $this->formatDuration($validated['duration_days'] ?? null, $validated['duration_nights'] ?? null),
            'announcement_date' => $validated['announcement_date'] ?? null,
            'total_pax' => $validated['total_pax'] ?? null,
            'image' => $validated['image'] ?? null,
            'gallery' => $validated['gallery'] ?? [],
            'addon_amenities' => $validated['addon_amenities'] ?? [],
            'included_services' => $validated['inclusions'] ?? [],
            'excluded_services' => $validated['exclusions'] ?? [],
            'itinerary' => $validated['itinerary'] ?? [],
            'featured' => isset($validated['is_featured']) ? (bool) $validated['is_featured'] : false,
            'status' => isset($validated['is_active']) ? (bool) $validated['is_active'] : true,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
        ];

        $package->update($packageData);

        return response()->json($package);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            $package = Package::withTrashed()->findOrFail($id);

            // Use DB transaction for safety
            DB::beginTransaction();

            try {
                if ($package->trashed()) {
                    // Force delete if already soft deleted
                    $package->forceDelete();
                } else {
                    // Soft delete
                    $package->delete();
                }

                DB::commit();

                return response()->json([
                    'message' => 'Package deleted successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            \Log::error('Database error deleting package', [
                'package_id' => $id,
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
                'sql_state' => $e->errorInfo[0] ?? null,
                'driver_code' => $e->errorInfo[1] ?? null,
                'sql_message' => $e->errorInfo[2] ?? null,
            ]);

            // Check for specific error types
            if (
                strpos($errorMessage, 'foreign key constraint') !== false ||
                strpos($errorMessage, '1451') !== false ||
                strpos($errorMessage, '23000') !== false
            ) {
                return response()->json([
                    'error' => 'Cannot delete package. It is referenced by other records.',
                    'message' => 'This package cannot be deleted because it is being used elsewhere in the system.'
                ], 422);
            }

            return response()->json([
                'error' => 'Database error while deleting package',
                'message' => $errorMessage
            ], 500);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Package not found',
                'message' => "Package with ID {$id} does not exist"
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error deleting package', [
                'package_id' => $id,
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error deleting package',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
