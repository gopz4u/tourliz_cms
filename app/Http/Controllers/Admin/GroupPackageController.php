<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupPackage;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Supplier;

class GroupPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            try {
                $query = GroupPackage::withTrashed()->with([
                    'destination' => function ($q) {
                        $q->withTrashed();
                    }
                ]);

                if ($request->has('search')) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                }

                // Filter by destination if provided
                if ($request->has('destination_id') && $request->destination_id) {
                    $query->where('destination_id', $request->destination_id);
                }

                $groupPackages = $query->orderBy('created_at', 'desc')->paginate(15);

                return response()->json($groupPackages);
            } catch (\Exception $e) {
                \Log::error('Error loading group packages: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Error loading group packages',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        return view('admin.group-packages.index');
    }

    /**
     * Get group packages by place
     */
    public function getByDestination($destinationId)
    {
        $groupPackages = GroupPackage::where('destination_id', $destinationId)
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($groupPackages);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('admin.group-packages.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'nullable|exists:destinations,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_ids' => 'nullable|array',
            'category' => 'nullable|string',
            'categories' => 'nullable|array',
            'package_category' => 'nullable|string|in:Honeymoon,Budget,Standard,Premium,Platinum',
            'includes_flight' => 'nullable',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'vehicle_type' => 'nullable|string|max:255',
            'accommodation_type' => 'nullable|string|max:255',
            'ticket_count' => 'nullable|integer|min:1',
            'ticket_name' => 'nullable|string|max:255',
            'addon_amenities' => 'nullable|array',
            'addon_amenities.*.type' => 'required|string|in:hotel,transport,ticket,accommodation,other',
            'addon_amenities.*.name' => 'required|string|max:255',
            'addon_amenities.*.value' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:group_packages,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'net_price' => 'nullable|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|min:0',
            'markup_amount' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'price_2_6' => 'nullable|numeric|min:0',
            'price_6_10' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:INR,USD,MYR,SGD,AED',
            'duration_days' => 'nullable|integer|min:0',
            'duration_nights' => 'nullable|integer|min:0',
            'announcement_date' => 'nullable|date',
            'total_pax' => 'nullable|integer|min:1',
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

        // Handle boolean fields
        $validated['includes_flight'] = isset($validated['includes_flight']) && ($validated['includes_flight'] == 1 || $validated['includes_flight'] === true || $validated['includes_flight'] === '1' || $validated['includes_flight'] === 'true');

        // Map form fields to database columns
        $groupPackageData = [
            'destination_id' => $validated['destination_id'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'supplier_ids' => $validated['supplier_ids'] ?? [],
            'category' => $validated['category'] ?? null,
            'categories' => $validated['categories'] ?? [],
            'package_category' => $validated['package_category'] ?? null,
            'includes_flight' => $validated['includes_flight'] ?? false,
            'star_rating' => $validated['star_rating'] ?? null,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'accommodation_type' => $validated['accommodation_type'] ?? null,
            'ticket_count' => $validated['ticket_count'] ?? null,
            'ticket_name' => $validated['ticket_name'] ?? null,
            'addon_amenities' => $validated['addon_amenities'] ?? [],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'net_price' => $validated['net_price'] ?? 0,
            'markup_percentage' => $validated['markup_percentage'] ?? 0,
            'markup_amount' => $validated['markup_amount'] ?? 0,
            'discount_price' => $validated['discount_price'] ?? null,
            'price_2_6' => $validated['price_2_6'] ?? null,
            'price_6_10' => $validated['price_6_10'] ?? null,
            'currency' => $validated['currency'] ?? 'INR',
            'duration' => $this->formatDuration($validated['duration_days'] ?? null, $validated['duration_nights'] ?? null),
            'announcement_date' => $validated['announcement_date'] ?? null,
            'total_pax' => $validated['total_pax'] ?? null,
            'image' => $validated['image'] ?? null,
            'gallery' => $validated['gallery'] ?? [],
            'included_services' => $validated['inclusions'] ?? [],
            'excluded_services' => $validated['exclusions'] ?? [],
            'itinerary' => $this->normalizeItinerary($validated['itinerary'] ?? []),
            'featured' => isset($validated['is_featured']) ? (bool) $validated['is_featured'] : false,
            'status' => isset($validated['is_active']) ? (bool) $validated['is_active'] : true,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
        ];

        $groupPackage = GroupPackage::create($groupPackageData);

        return response()->json($groupPackage, 201);
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $groupPackage = GroupPackage::withTrashed()->with([
                'destination' => function ($q) {
                    $q->withTrashed();
                }
            ])->findOrFail($id);
            return response()->json($groupPackage);
        } catch (\Exception $e) {
            \Log::error('Error loading group package: ' . $e->getMessage());
            return response()->json([
                'error' => 'Group package not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('admin.group-packages.edit', ['id' => $id, 'suppliers' => $suppliers]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $groupPackage = GroupPackage::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'destination_id' => 'nullable|exists:destinations,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_ids' => 'nullable|array',
            'category' => 'nullable|string',
            'categories' => 'nullable|array',
            'package_category' => 'nullable|string|in:Honeymoon,Budget,Standard,Premium,Platinum',
            'includes_flight' => 'nullable',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'vehicle_type' => 'nullable|string|max:255',
            'accommodation_type' => 'nullable|string|max:255',
            'ticket_count' => 'nullable|integer|min:1',
            'ticket_name' => 'nullable|string|max:255',
            'addon_amenities' => 'nullable|array',
            'addon_amenities.*.type' => 'required|string|in:hotel,transport,ticket,accommodation,other',
            'addon_amenities.*.name' => 'required|string|max:255',
            'addon_amenities.*.value' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('group_packages')->ignore($groupPackage->id)],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'net_price' => 'nullable|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|min:0',
            'markup_amount' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'price_2_6' => 'nullable|numeric|min:0',
            'price_6_10' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:INR,USD,MYR,SGD,AED',
            'duration_days' => 'nullable|integer|min:0',
            'duration_nights' => 'nullable|integer|min:0',
            'announcement_date' => 'nullable|date',
            'total_pax' => 'nullable|integer|min:1',
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
        $groupPackageData = [
            'destination_id' => $validated['destination_id'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'supplier_ids' => $validated['supplier_ids'] ?? [],
            'category' => $validated['category'] ?? null,
            'categories' => $validated['categories'] ?? [],
            'package_category' => $validated['package_category'] ?? null,
            'includes_flight' => $validated['includes_flight'] ?? false,
            'star_rating' => $validated['star_rating'] ?? null,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'accommodation_type' => $validated['accommodation_type'] ?? null,
            'ticket_count' => $validated['ticket_count'] ?? null,
            'ticket_name' => $validated['ticket_name'] ?? null,
            'addon_amenities' => $validated['addon_amenities'] ?? [],
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $groupPackage->slug,
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'net_price' => $validated['net_price'] ?? 0,
            'markup_percentage' => $validated['markup_percentage'] ?? 0,
            'markup_amount' => $validated['markup_amount'] ?? 0,
            'discount_price' => $validated['discount_price'] ?? null,
            'price_2_6' => $validated['price_2_6'] ?? null,
            'price_6_10' => $validated['price_6_10'] ?? null,
            'currency' => $validated['currency'] ?? 'INR',
            'duration' => $this->formatDuration($validated['duration_days'] ?? null, $validated['duration_nights'] ?? null),
            'announcement_date' => $validated['announcement_date'] ?? null,
            'total_pax' => $validated['total_pax'] ?? null,
            'image' => $validated['image'] ?? null,
            'gallery' => $validated['gallery'] ?? [],
            'included_services' => $validated['inclusions'] ?? [],
            'excluded_services' => $validated['exclusions'] ?? [],
            'itinerary' => $this->normalizeItinerary($validated['itinerary'] ?? []),
            'featured' => isset($validated['is_featured']) ? (bool) $validated['is_featured'] : false,
            'status' => isset($validated['is_active']) ? (bool) $validated['is_active'] : true,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
        ];

        $groupPackage->update($groupPackageData);

        return response()->json($groupPackage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $groupPackage = GroupPackage::withTrashed()->findOrFail($id);

            // Use DB transaction for safety
            DB::beginTransaction();

            try {
                if ($groupPackage->trashed()) {
                    // Force delete if already soft deleted
                    $groupPackage->forceDelete();
                } else {
                    // Soft delete
                    $groupPackage->delete();
                }

                DB::commit();

                return response()->json([
                    'message' => 'Group package deleted successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            \Log::error('Database error deleting group package', [
                'group_package_id' => $id,
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
            ]);

            if (
                strpos($errorMessage, 'foreign key constraint') !== false ||
                strpos($errorMessage, '1451') !== false ||
                strpos($errorMessage, '23000') !== false
            ) {
                return response()->json([
                    'error' => 'Cannot delete group package. It is referenced by other records.',
                    'message' => 'This group package cannot be deleted because it is being used elsewhere in the system.'
                ], 422);
            }

            return response()->json([
                'error' => 'Database error while deleting group package',
                'message' => $errorMessage
            ], 500);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Group package not found',
                'message' => "Group package with ID {$id} does not exist"
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error deleting group package', [
                'group_package_id' => $id,
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Error deleting group package',
                'message' => $e->getMessage()
            ], 500);
        }
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
                $hotel = ['name' => $hotelInput, 'type' => '', 'price_per_night' => 0, 'currency' => 'INR'];
            } elseif (is_array($hotelInput)) {
                $hotel = $hotelInput;
            } else {
                $hotel = ['name' => '', 'type' => '', 'price_per_night' => 0, 'currency' => 'INR'];
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
}
