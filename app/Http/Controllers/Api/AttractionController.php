<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Models\Attraction;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    /**
     * Display a listing of attractions.
     */
    public function index(Request $request)
    {
        $query = Attraction::with(['destination'])
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

        // Filter featured attractions
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
        $attractions = $query->paginate($perPage);

        return AttractionResource::collection($attractions);
    }

    /**
     * Display the specified attraction.
     */
    public function show($slug)
    {
        $attraction = Attraction::with(['destination'])
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return new AttractionResource($attraction);
    }

    /**
     * Get attraction gallery images
     */
    public function gallery($id)
    {
        $attraction = Attraction::where('id', $id)
            ->where('status', true)
            ->firstOrFail();

        $gallery = $attraction->gallery ?? [];
        
        return response()->json([
            'attraction_id' => $attraction->id,
            'attraction_name' => $attraction->name,
            'gallery' => array_map(function($img) {
                return getImageUrl($img);
            }, $gallery),
        ]);
    }
}

