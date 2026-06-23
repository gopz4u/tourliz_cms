<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
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
                $query = Service::withTrashed()->with([
                    'destination' => function ($q) {
                        $q->withTrashed();
                    },
                    'package' => function ($q) {
                        $q->withTrashed();
                    },
                    'supplier'
                ]);

                if ($request->has('search')) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                }

                // Filter by destination if provided
                if ($request->has('destination_id') && $request->destination_id) {
                    $query->where('destination_id', $request->destination_id);
                }

                // Filter by package if provided
                if ($request->has('package_id') && $request->package_id) {
                    $query->where('package_id', $request->package_id);
                }

                // Filter by category if provided
                if ($request->has('category') && $request->category) {
                    $category = $request->category;
                    if ($category === 'Transport') {
                        $query->whereIn('category', ['Transport', 'Airport Pickup', 'Airport Drop']);
                    } else {
                        $query->where('category', $category);
                    }
                }

                $services = $query->orderBy('created_at', 'desc')->paginate(15);

                return response()->json($services);
            } catch (\Exception $e) {
                \Log::error('Error loading services: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Error loading services',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        return view('admin.services.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.services.create');
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
            'package_id' => 'nullable|exists:packages,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_2_6' => 'nullable|numeric|min:0',
            'price_6_10' => 'nullable|numeric|min:0',
            'announcement_date' => 'nullable|date',
            'total_pax' => 'nullable|integer|min:1',
            'currency' => 'nullable|string|in:INR,USD,MYR,SGD,AED',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'category' => 'required|string|in:Entry Tickets,Hotels,Transport,Airport Pickup,Airport Drop,Activities,Meals,Other Services',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'vehicle_type' => 'nullable|string|max:255',
            'accommodation_type' => 'nullable|string|max:255',
            'ticket_count' => 'nullable|integer|min:1',
            'ticket_name' => 'nullable|string|max:255',
            'addon_amenities' => 'nullable|array',
            'addon_amenities.*.type' => 'required|string|in:hotel,transport,ticket,accommodation,other',
            'addon_amenities.*.name' => 'required|string|max:255',
            'addon_amenities.*.value' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_featured' => 'nullable',
            'is_active' => 'nullable',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'MYR';

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $count = 1;
            while (Service::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            $validated['slug'] = $slug;
        }

        // Handle boolean fields - convert to boolean (accepts 0, 1, true, false, "0", "1", etc.)
        $validated['is_featured'] = isset($validated['is_featured']) && ($validated['is_featured'] == 1 || $validated['is_featured'] === true || $validated['is_featured'] === '1' || $validated['is_featured'] === 'true');
        $validated['is_active'] = isset($validated['is_active']) && ($validated['is_active'] == 1 || $validated['is_active'] === true || $validated['is_active'] === '1' || $validated['is_active'] === 'true') ? true : (isset($validated['is_active']) ? false : true);

        // Ensure gallery and addon_amenities are not null
        $validated['gallery'] = $validated['gallery'] ?? [];
        $validated['addon_amenities'] = $validated['addon_amenities'] ?? [];

        $service = Service::create($validated);

        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::withTrashed()->with([
            'destination' => function ($q) {
                $q->withTrashed();
            },
            'package' => function ($q) {
                $q->withTrashed();
            },
            'supplier'
        ])->findOrFail($id);
        return response()->json($service);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.services.edit', ['id' => $id]);
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
        $service = Service::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'destination_id' => 'nullable|exists:destinations,id',
            'package_id' => 'nullable|exists:packages,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('services')->ignore($service->id)],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_2_6' => 'nullable|numeric|min:0',
            'price_6_10' => 'nullable|numeric|min:0',
            'announcement_date' => 'nullable|date',
            'total_pax' => 'nullable|integer|min:1',
            'currency' => 'nullable|string|in:INR,USD,MYR,SGD,AED',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'category' => 'required|string|in:Entry Tickets,Hotels,Transport,Airport Pickup,Airport Drop,Other Services',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'vehicle_type' => 'nullable|string|max:255',
            'accommodation_type' => 'nullable|string|max:255',
            'ticket_count' => 'nullable|integer|min:1',
            'ticket_name' => 'nullable|string|max:255',
            'addon_amenities' => 'nullable|array',
            'addon_amenities.*.type' => 'required|string|in:hotel,transport,ticket,accommodation,other',
            'addon_amenities.*.name' => 'required|string|max:255',
            'addon_amenities.*.value' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_featured' => 'nullable',
            'is_active' => 'nullable',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // Handle boolean fields - convert to boolean (accepts 0, 1, true, false, "0", "1", etc.)
        $validated['is_featured'] = isset($validated['is_featured']) && ($validated['is_featured'] == 1 || $validated['is_featured'] === true || $validated['is_featured'] === '1' || $validated['is_featured'] === 'true');
        $validated['is_active'] = isset($validated['is_active']) && ($validated['is_active'] == 1 || $validated['is_active'] === true || $validated['is_active'] === '1' || $validated['is_active'] === 'true') ? true : (isset($validated['is_active']) ? false : true);

        // Ensure gallery and addon_amenities are not null
        $validated['gallery'] = $validated['gallery'] ?? [];
        $validated['addon_amenities'] = $validated['addon_amenities'] ?? [];

        $service->update($validated);

        return response()->json($service);
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
            $service = Service::findOrFail($id);
            $service->delete();

            return response()->json(['message' => 'Service deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Service not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error deleting service: ' . $e->getMessage(), [
                'service_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error deleting service: ' . $e->getMessage()
            ], 500);
        }
    }
}
