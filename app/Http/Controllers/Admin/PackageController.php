<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
                $query = Package::query();

                if ($request->has('trash')) {
                    $query->onlyTrashed();
                }

                $query->withCount('reviews')
                    ->with([
                        'country',
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

                $packages = $query->orderBy('created_at', 'desc')->paginate(100);

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
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($packages);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = \App\Models\Country::where('status', true)->orderBy('name')->get();
        $suppliers = \App\Models\Supplier::with('destination')->where('is_active', true)->orderBy('name')->get();
        $hotels = \App\Models\Hotel::with(['roomTypes', 'supplier.destination'])->where('is_active', true)->orderBy('name')->get();
        $activities = \App\Models\Activity::with('supplier.destination')->where('is_active', true)->orderBy('name')->get();
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $transportRoutes = \App\Models\Transport::with('supplier.destination')->get();
        $entryTickets = \App\Models\EntryTicket::with('supplier.destination')->get();
        $meals = \App\Models\Meal::with('supplier.destination')->get();
        $touristSpots = \App\Models\TouristSpot::with(['supplier', 'destination', 'country'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.packages.create', compact(
            'countries',
            'suppliers',
            'hotels',
            'activities',
            'destinations',
            'transportRoutes',
            'entryTickets',
            'meals',
            'touristSpots'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'destination_id' => 'nullable|exists:destinations,id',
            'hotel_id' => 'nullable|exists:hotels,id',
            'destination_ids' => 'nullable|array',
            'destination_ids.*' => 'exists:destinations,id',
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
            'net_price' => 'nullable|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|min:0',
            'markup_amount' => 'nullable|numeric|min:0',
            'gst_percentage' => 'nullable|numeric|min:0',
            'tcs_percentage' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'price_2_6' => 'nullable|numeric|min:0',
            'price_6_10' => 'nullable|numeric|min:0',
            'announcement_date' => 'nullable|date',
            'total_pax' => 'nullable|integer|min:1',
            'min_pax' => 'nullable|integer|min:1',
            'max_pax' => 'nullable|integer|min:1',
            'duration_days' => 'nullable|integer|min:0',
            'duration_nights' => 'nullable|integer|min:0',
            'image' => 'nullable',
            'gallery' => 'nullable',
            'inclusions' => 'nullable',
            'exclusions' => 'nullable',
            'itinerary' => 'nullable',
            'is_featured' => 'nullable',
            'is_active' => 'nullable',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'availability' => 'nullable|array',
        ]);

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $count = 1;
            while (Package::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            $validated['slug'] = $slug;
        }

        // Handle File Uploads
        $heroPath = $this->handleFileUpload($request, 'image');
        $galleryPaths = $this->handleMultipleFileUpload($request, 'gallery');

        $itineraryData = json_decode($request->itinerary_data, true) ?? [];
        $itineraryData = $this->sanitizeUtf8($itineraryData);

        // Map form fields to database columns
        $packageData = [
            'country_id' => $validated['country_id'] ?? null,
            'country_ids' => $this->sanitizeUtf8($validated['country_ids'] ?? []),
            'destination_id' => isset($validated['destination_ids']) && count($validated['destination_ids']) > 0 ? $validated['destination_ids'][0] : null,
            'destination_ids' => $this->sanitizeUtf8($validated['destination_ids'] ?? []),
            'supplier_ids' => $this->sanitizeUtf8($validated['supplier_ids'] ?? []),
            'supplier_id' => isset($validated['supplier_ids']) && count($validated['supplier_ids']) > 0 ? $validated['supplier_ids'][0] : null,
            'categories' => $this->sanitizeUtf8($validated['categories'] ?? []),
            'category' => isset($validated['categories']) && count($validated['categories']) > 0 ? $validated['categories'][0] : null,
            'package_category' => $validated['package_category'] ?? null,
            'star_rating' => $validated['star_rating'] ?? null,
            'vehicle_type' => $this->sanitizeUtf8($validated['vehicle_type'] ?? null),
            'accommodation_type' => $this->sanitizeUtf8($validated['accommodation_type'] ?? null),
            'ticket_count' => $validated['ticket_count'] ?? null,
            'ticket_name' => $this->sanitizeUtf8($validated['ticket_name'] ?? null),
            'name' => $this->sanitizeUtf8($validated['name']),
            'slug' => $validated['slug'],
            'description' => $this->sanitizeUtf8($validated['description'] ?? null),
            'short_description' => $this->sanitizeUtf8($validated['short_description'] ?? null),
            'price' => $validated['price'],
            'net_price' => $request->net_price ?? 0,
            'markup_percentage' => $request->markup_percentage ?? 0,
            'markup_amount' => $request->markup_amount ?? 0,
            'gst_percentage' => $request->gst_percentage ?? 0,
            'tcs_percentage' => $request->tcs_percentage ?? 0,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_price' => $validated['discount_price'] ?? null,
            'currency' => $validated['currency'] ?? 'MYR',
            'duration' => $this->formatDuration($validated['duration_days'] ?? null, $validated['duration_nights'] ?? null),
            'announcement_date' => $validated['announcement_date'] ?? null,
            'total_pax' => $validated['total_pax'] ?? null,
            'min_pax' => $validated['min_pax'] ?? 1,
            'max_pax' => $validated['max_pax'] ?? null,
            'image' => $heroPath ?? null,
            'gallery' => $this->sanitizeUtf8($galleryPaths ?? []),
            'addon_amenities' => $this->sanitizeUtf8($validated['addon_amenities'] ?? []),
            'highlights' => $this->parseListInput($request->highlights),
            'inclusions' => $this->parseListInput($request->inclusions ?? $request->included_services),
            'exclusions' => $this->parseListInput($request->exclusions ?? $request->excluded_services),
            'included_services' => $this->sanitizeUtf8($request->included_services ?? (is_array($request->inclusions) ? implode("\n", $request->inclusions) : $request->inclusions)),
            'excluded_services' => $this->sanitizeUtf8($request->excluded_services ?? (is_array($request->exclusions) ? implode("\n", $request->exclusions) : $request->exclusions)),
            'itinerary' => $itineraryData,
            'featured' => isset($validated['is_featured']) ? (bool) $validated['is_featured'] : false,
            'status' => isset($validated['is_active']) && $validated['is_active'] ? 'active' : 'inactive',
            'meta_title' => $this->sanitizeUtf8($validated['meta_title'] ?? null),
            'meta_description' => $this->sanitizeUtf8($validated['meta_description'] ?? null),
            'meta_keywords' => $this->sanitizeUtf8($validated['meta_keywords'] ?? null),
            'availability' => $this->sanitizeUtf8($validated['availability'] ?? null),
            'is_trending' => $request->has('is_trending'),
            'cancellation_policy' => $this->sanitizeUtf8($request->cancellation_policy),
            'terms' => $this->sanitizeUtf8($request->terms),
        ];

        \DB::beginTransaction();
        try {
            $package = Package::create($packageData);

            // Save Structured Itinerary
            $itineraryData = json_decode($request->itinerary_data, true) ?? [];
            $this->saveStructuredItinerary($package, $itineraryData);

            \DB::commit();
            return response()->json($package, 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Package creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Error creating package: ' . $e->getMessage()], 500);
        }
    }

    private function handleFileUpload($request, $key)
    {
        if ($request->hasFile($key)) {
            $file = $request->file($key);
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('packages', $filename, 's3');
            return Storage::disk('s3')->url($path);
        }
        return null;
    }

    private function handleMultipleFileUpload($request, $key)
    {
        $urls = [];
        if ($request->hasFile($key)) {
            foreach ($request->file($key) as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('packages/gallery', $filename, 's3');
                $urls[] = Storage::disk('s3')->url($path);
            }
        }
        return $urls;
    }

    /**
     * Save structured itinerary data to relational tables
     */
    private function saveStructuredItinerary(Package $package, array $itinerary)
    {
        // Explicitly clean up all existing days and their child relations first to prevent any foreign key constraint issues
        foreach ($package->days as $existingDay) {
            $existingDay->hotels()->delete();
            $existingDay->transports()->delete();
            $existingDay->activities()->delete();
            $existingDay->attractions()->delete();
            $existingDay->meals_list()->delete();
            $existingDay->delete();
        }
        $package->days()->delete();

        foreach ($itinerary as $dayData) {
            $day = $package->days()->create([
                'day_number' => $dayData['day_number'] ?? ($dayData['day'] ?? 1),
                'title' => $this->sanitizeUtf8($dayData['title'] ?? null),
                'description' => $this->sanitizeUtf8($dayData['description'] ?? ($dayData['notes'] ?? null)),
                'highlights' => $this->parseListInput($dayData['highlights'] ?? []),
                'inclusions' => $this->parseListInput($dayData['inclusions'] ?? []),
                'exclusions' => $this->parseListInput($dayData['exclusions'] ?? []),
                'meal_plan' => $dayData['meals'] ?? [],
            ]);

            // Link Multiple Hotels
            if (!empty($dayData['hotels']) && is_array($dayData['hotels'])) {
                foreach ($dayData['hotels'] as $h) {
                    if (!empty($h['hotel_id'])) {
                        $day->hotels()->create([
                            'hotel_id' => $h['hotel_id'],
                            'meal_plan_code' => 'CP',
                            'is_primary' => count($dayData['hotels']) === 1
                        ]);
                    }
                }
            } elseif (!empty($dayData['hotel_id'])) { // Fallback for legacy format
                $day->hotels()->create(['hotel_id' => $dayData['hotel_id'], 'meal_plan_code' => 'CP', 'is_primary' => true]);
            }

            // Link Multiple Transports
            if (!empty($dayData['transports']) && is_array($dayData['transports'])) {
                foreach ($dayData['transports'] as $t) {
                    if (!empty($t['transport_id'])) {
                        $day->transports()->create(['transport_id' => $t['transport_id']]);
                    }
                }
            } elseif (!empty($dayData['transport_id'])) { // Fallback
                $day->transports()->create(['transport_id' => $dayData['transport_id']]);
            }

            // Link Multiple Activities
            if (!empty($dayData['activities']) && is_array($dayData['activities'])) {
                foreach ($dayData['activities'] as $a) {
                    if (!empty($a['activity_id'])) {
                        $day->activities()->create(['activity_id' => $a['activity_id']]);
                    }
                }
            } elseif (!empty($dayData['activity_ids'])) { // Fallback
                foreach ($dayData['activity_ids'] as $id) {
                    if ($id)
                        $day->activities()->create(['activity_id' => $id]);
                }
            }

            // Link Multiple Entry Tickets (Attractions)
            if (!empty($dayData['tickets']) && is_array($dayData['tickets'])) {
                foreach ($dayData['tickets'] as $t) {
                    if (!empty($t['ticket_id'])) {
                        $day->attractions()->create(['attraction_id' => $t['ticket_id']]);
                    }
                }
            }

            // Link Multiple Meals
            if (!empty($dayData['meals_list']) && is_array($dayData['meals_list'])) {
                foreach ($dayData['meals_list'] as $m) {
                    if (!empty($m['meal_id'])) {
                        $day->meals_list()->create(['meal_id' => $m['meal_id']]);
                    }
                }
            }
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
                    'lunch' => in_array('Lunch', $mealsInput) ? 'Included' : 'Not included',
                    'dinner' => in_array('Dinner', $mealsInput) ? 'Included' : 'Not included',
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
                'day' => (int) ($day['day'] ?? 1),
                'title' => $day['title'] ?? ('Day ' . ($day['day'] ?? 1)),
                'places' => $places,
                'activities' => $activities,
                'transport' => $transport,
                'hotel' => $hotel,
                'meals' => $meals,
                'notes' => $day['description'] ?? ($day['notes'] ?? ''),
                'destinations' => $day['destinations'] ?? [],
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
        $package = Package::withTrashed()->with(['days.hotels', 'days.transports', 'days.activities', 'days.attractions', 'days.meals_list.meal'])->findOrFail($id);
        $countries = \App\Models\Country::where('status', true)->orderBy('name')->get();
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $hotels = \App\Models\Hotel::with(['roomTypes', 'supplier.destination'])->orderBy('name')->get();
        $transportRoutes = \App\Models\Transport::with('supplier.destination')->orderBy('name')->get();
        $activities = \App\Models\Activity::with('supplier.destination')->orderBy('name')->get();
        $entryTickets = \App\Models\EntryTicket::with('supplier')->orderBy('attraction_name')->get();
        $meals = \App\Models\Meal::with('supplier')->orderBy('name')->get();
        $suppliers = \App\Models\Supplier::with('destination')->where('is_active', true)->orderBy('name')->get();
        $touristSpots = \App\Models\TouristSpot::with(['supplier', 'destination', 'country'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.packages.edit', [
            'id' => $id,
            'package' => $package,
            'countries' => $countries,
            'destinations' => $destinations,
            'hotels' => $hotels,
            'transportRoutes' => $transportRoutes,
            'activities' => $activities,
            'entryTickets' => $entryTickets,
            'meals' => $meals,
            'suppliers' => $suppliers,
            'touristSpots' => $touristSpots
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $package = Package::withTrashed()->findOrFail($id);

        // Map UI checkboxes to booleans
        $request->merge([
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'is_trending' => $request->has('is_trending'),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'destination_id' => 'nullable|exists:destinations,id',
            'destination_ids' => 'nullable|array',
            'destination_ids.*' => 'exists:destinations,id',
            'package_category' => 'nullable|string',
            'price' => 'required|numeric',
            'net_price' => 'nullable|numeric',
            'markup_percentage' => 'nullable|numeric',
            'markup_amount' => 'nullable|numeric',
            'gst_percentage' => 'nullable|numeric',
            'tcs_percentage' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'min_pax' => 'nullable|integer',
            'max_pax' => 'nullable|integer',
            'includes_flight' => 'nullable',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // Handle File Uploads
        $heroPath = $this->handleFileUpload($request, 'image') ?: $package->image;
        $retainedGallery = $request->retained_gallery ?? [];
        $newGalleryPaths = $this->handleMultipleFileUpload($request, 'gallery');
        $galleryPaths = array_merge($retainedGallery, $newGalleryPaths);

        $itineraryData = json_decode($request->itinerary_data, true) ?? [];
        $itineraryData = $this->sanitizeUtf8($itineraryData);

        $packageData = [
            'name' => $this->sanitizeUtf8($validated['name']),
            'country_id' => $validated['country_id'] ?? $package->country_id,
            'country_ids' => $this->sanitizeUtf8($request->country_ids ?? $package->country_ids),
            'destination_id' => isset($request->destination_ids) && count($request->destination_ids) > 0 ? $request->destination_ids[0] : null,
            'destination_ids' => $this->sanitizeUtf8($request->destination_ids ?? []),
            'supplier_ids' => $this->sanitizeUtf8($request->supplier_ids ?? $package->supplier_ids ?? []),
            'supplier_id' => isset($request->supplier_ids) && count($request->supplier_ids) > 0 ? $request->supplier_ids[0] : $package->supplier_id,
            'categories' => $this->sanitizeUtf8($request->categories ?? $package->categories ?? []),
            'category' => isset($request->categories) && count($request->categories) > 0 ? $request->categories[0] : $package->category,
            'package_category' => $validated['package_category'] ?? null,
            'price' => $validated['price'],
            'net_price' => $request->net_price ?? $package->net_price,
            'markup_percentage' => $request->markup_percentage ?? $package->markup_percentage,
            'markup_amount' => $request->markup_amount ?? $package->markup_amount,
            'gst_percentage' => $request->gst_percentage ?? $package->gst_percentage,
            'tcs_percentage' => $request->tcs_percentage ?? $package->tcs_percentage,
            'tax_amount' => $request->tax_amount ?? $package->tax_amount,
            'min_pax' => $validated['min_pax'] ?? 1,
            'max_pax' => $validated['max_pax'] ?? 10,
            'includes_flight' => $request->includes_flight == '1',
            'image' => $heroPath,
            'gallery' => $this->sanitizeUtf8($galleryPaths),
            'short_description' => $this->sanitizeUtf8($request->short_description),
            'highlights' => $this->parseListInput($request->highlights ?? $request->short_description),
            'inclusions' => $this->parseListInput($request->inclusions ?? $request->included_services),
            'exclusions' => $this->parseListInput($request->exclusions ?? $request->excluded_services),
            'included_services' => $this->sanitizeUtf8($request->included_services ?? (is_array($request->inclusions) ? implode("\n", $request->inclusions) : $request->inclusions)),
            'excluded_services' => $this->sanitizeUtf8($request->excluded_services ?? (is_array($request->exclusions) ? implode("\n", $request->exclusions) : $request->exclusions)),
            'cancellation_policy' => $this->sanitizeUtf8($request->cancellation_policy),
            'terms' => $this->sanitizeUtf8($request->terms),
            'itinerary' => $itineraryData,
            'featured' => (bool) $request->is_featured,
            'status' => $request->is_active ? 'active' : 'inactive',
            'is_trending' => (bool) $request->is_trending,
            'meta_title' => $this->sanitizeUtf8($validated['meta_title'] ?? null),
            'meta_description' => $this->sanitizeUtf8($validated['meta_description'] ?? null),
            'meta_keywords' => $this->sanitizeUtf8($validated['meta_keywords'] ?? null),
            'duration' => $this->formatDuration($request->duration_days, $request->duration_nights),
        ];

        \DB::beginTransaction();
        try {
            $package->update($packageData);

            // Save Structured Itinerary
            $this->saveStructuredItinerary($package, $itineraryData);

            \DB::commit();
            return response()->json($package);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Package update failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Error updating package',
                'message' => $e->getMessage()
            ], 500);
        }
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
    /**
     * Duplicate the specified resource.
     */
    public function duplicate($id): \Illuminate\Http\JsonResponse
    {
        try {
            $original = Package::findOrFail($id);
            $new = $original->replicate();

            // Append " (Copy)" to the name
            $new->name = $original->name . ' (Copy)';

            // Generate a unique slug
            $slug = Str::slug($new->name);
            $count = Package::where('slug', 'like', $slug . '%')->count();
            $new->slug = $count ? $slug . '-' . ($count + 1) : $slug;

            // Set status to inactive by default for safety
            $new->status = 'inactive';
            $new->featured = false;

            $new->save();

            return response()->json([
                'message' => 'Package duplicated successfully',
                'package' => $new
            ]);
        } catch (\Exception $e) {
            \Log::error('Error duplicating package: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error duplicating package',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sanitize string or array recursively to valid UTF-8
     */
    private function sanitizeUtf8($value)
    {
        if (is_string($value)) {
            $str = mb_convert_encoding($value, 'UTF-8', 'UTF-8, Windows-1252, ISO-8859-1, ASCII');
            return mb_scrub($str, 'UTF-8');
        }

        if (is_array($value)) {
            $clean = [];
            foreach ($value as $k => $v) {
                $cleanKey = is_string($k) ? $this->sanitizeUtf8($k) : $k;
                $clean[$cleanKey] = $this->sanitizeUtf8($v);
            }
            return $clean;
        }

        return $value;
    }

    /**
     * Helper to normalize text/array inputs into array of strings
     */
    private function parseListInput($input): array
    {
        $input = $this->sanitizeUtf8($input);

        if (is_array($input)) {
            $cleaned = [];
            foreach ($input as $item) {
                if (is_string($item) || is_numeric($item)) {
                    $str = trim((string) $item, " \t\n\r\0\x0B-•*\xC2\xA0");
                    if ($str !== '') {
                        $cleaned[] = $str;
                    }
                }
            }
            return array_values($cleaned);
        }

        if (is_string($input)) {
            $text = preg_replace('/<\/(p|li|h[1-6]|div)>/i', "\n", $input);
            $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
            $text = strip_tags($text);
            $lines = explode("\n", $text);
            $clean = [];
            foreach ($lines as $line) {
                $lineStr = html_entity_decode($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $lineStr = $this->sanitizeUtf8($lineStr);
                $lineStr = trim($lineStr, " \t\n\r\0\x0B-•*\xC2\xA0");
                if ($lineStr !== '') {
                    $clean[] = $lineStr;
                }
            }
            return array_values($clean);
        }

        return [];
    }
}
