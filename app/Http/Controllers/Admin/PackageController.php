<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Package::withTrashed();

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $packages = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json($packages);
        }
        
        return view('admin.packages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.packages.create');
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
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packages,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
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
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'duration' => $this->formatDuration($validated['duration_days'] ?? null, $validated['duration_nights'] ?? null),
            'image' => $validated['image'] ?? null,
            'gallery' => $validated['gallery'] ?? null,
            'included_services' => $validated['inclusions'] ?? null,
            'excluded_services' => $validated['exclusions'] ?? null,
            'itinerary' => $validated['itinerary'] ?? null,
            'featured' => isset($validated['is_featured']) ? (bool)$validated['is_featured'] : false,
            'status' => isset($validated['is_active']) ? (bool)$validated['is_active'] : true,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
        ];

        $package = Package::create($packageData);

        return response()->json($package, 201);
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
        $package = Package::withTrashed()->findOrFail($id);
        return response()->json($package);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.packages.edit', ['id' => $id]);
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
        $package = Package::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('packages')->ignore($package->id)],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
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

        // Map form fields to database columns
        $packageData = [
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $package->slug,
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'duration' => $this->formatDuration($validated['duration_days'] ?? null, $validated['duration_nights'] ?? null),
            'image' => $validated['image'] ?? null,
            'gallery' => $validated['gallery'] ?? null,
            'included_services' => $validated['inclusions'] ?? null,
            'excluded_services' => $validated['exclusions'] ?? null,
            'itinerary' => $validated['itinerary'] ?? null,
            'featured' => isset($validated['is_featured']) ? (bool)$validated['is_featured'] : false,
            'status' => isset($validated['is_active']) ? (bool)$validated['is_active'] : true,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
        ];

        $package->update($packageData);

        return response()->json($package);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();

        return response()->json(['message' => 'Package deleted successfully']);
    }
}
