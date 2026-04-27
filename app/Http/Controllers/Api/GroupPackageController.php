<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupPackageResource;
use App\Models\GroupPackage;
use Illuminate\Http\Request;

class GroupPackageController extends Controller
{
    /**
     * Display a listing of group packages.
     */
    public function index(Request $request)
    {
        $query = GroupPackage::with(['destination'])
            ->where('status', true);

        // Filter by place_id
        if ($request->has('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        // Filter by place slug
        if ($request->has('place_slug')) {
            $query->whereHas('place', function($q) use ($request) {
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

        return GroupPackageResource::collection($packages);
    }

    /**
     * Display the specified group package.
     */
    public function show($slug)
    {
        $package = GroupPackage::with(['destination'])
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return new GroupPackageResource($package);
    }

    /**
     * Get group package gallery images
     */
    public function gallery($id)
    {
        $package = GroupPackage::where('id', $id)
            ->where('status', true)
            ->firstOrFail();

        $gallery = $package->gallery ?? [];
        
        return response()->json([
            'package_id' => $package->id,
            'package_name' => $package->name,
            'gallery' => array_map(function($img) {
                return getImageUrl($img);
            }, $gallery),
        ]);
    }
}

