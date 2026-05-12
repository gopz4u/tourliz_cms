<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of packages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Package::with(['destination'])->where('status', 'active');

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by duration
        if ($request->has('duration_days')) {
            $query->where('duration_days', $request->duration_days);
        }

        // Filter featured packages
        if ($request->has('featured') && $request->featured == 'true') {
            $query->where('is_featured', true);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $packages = $query->paginate($perPage);

        return PackageResource::collection($packages);
    }

    /**
     * Display the specified package.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $package = Package::with(['destination'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return new PackageResource($package);
    }

    /**
     * Get package gallery images
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function gallery($id)
    {
        $package = Package::where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

        $gallery = $package->gallery ?? [];

        return response()->json([
            'package_id' => $package->id,
            'package_name' => $package->name,
            'gallery' => array_map(function ($img) {
                return getImageUrl($img);
            }, $gallery),
        ]);
    }

    /**
     * Get packages by destination (place)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destinations(Request $request)
    {
        $query = Package::with(['destination'])
            ->where('status', 'active');

        // Filter by place_id if provided
        if ($request->has('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        // Filter by place slug if provided
        if ($request->has('place_slug')) {
            $query->whereHas('place', function ($q) use ($request) {
                $q->where('slug', $request->place_slug);
            });
        }

        // Filter featured packages
        if ($request->has('featured') && $request->featured == 'true') {
            $query->where('featured', true);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $packages = $query->paginate($perPage);

        return PackageResource::collection($packages);
    }

    /**
     * Get packages by category
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function category(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
        ]);

        $query = Package::with(['destination'])
            ->where('status', 'active')
            ->where('category', $request->category);

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter featured packages
        if ($request->has('featured') && $request->featured == 'true') {
            $query->where('featured', true);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $packages = $query->paginate($perPage);

        return PackageResource::collection($packages);
    }

    /**
     * Get package itinerary
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function getItinerary($slug)
    {
        $package = Package::with(['destination'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        if (!$package->hasItinerary()) {
            return response()->json([
                'message' => 'This package does not have an itinerary yet.',
                'package' => [
                    'id' => $package->id,
                    'name' => $package->name,
                    'slug' => $package->slug,
                ]
            ], 404);
        }

        return new \App\Http\Resources\ItineraryResource([
            'package' => $package,
            'itinerary' => $package->itinerary
        ]);
    }

    /**
     * Generate sample itinerary for a package
     *
     * @param  string  $slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateSampleItinerary($slug, Request $request)
    {
        $package = Package::where('slug', $slug)->firstOrFail();

        // Get days from request or calculate from package duration
        $days = $request->get('days', 3);
        if ($package->duration) {
            if (preg_match('/(\d+)\s*days?/i', $package->duration, $matches)) {
                $days = (int) $matches[1];
            }
        }

        // Generate sample itinerary
        $itinerary = \App\Helpers\ItineraryHelper::generateSampleItinerary($days, $package->destination_id);

        // Update package with generated itinerary
        $package->itinerary = $itinerary;
        $package->save();

        return response()->json([
            'message' => 'Sample itinerary generated successfully',
            'package' => [
                'id' => $package->id,
                'name' => $package->name,
                'slug' => $package->slug,
            ],
            'itinerary' => new \App\Http\Resources\ItineraryResource([
                'package' => $package,
                'itinerary' => $itinerary
            ])
        ]);
    }
}
