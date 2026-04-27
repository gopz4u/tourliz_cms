<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Display a listing of places.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Destination::where('status', true);

        // Filter by country
        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', $request->location);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        // Filter featured places
        if ($request->has('featured') && $request->featured == 'true') {
            $query->where('featured', true);
        }

        // Filter by status (active places)
        $query->where('status', true);

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
        $places = $query->paginate($perPage);

        return DestinationResource::collection($places);
    }

    /**
     * Display the specified place.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $place = Destination::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return new DestinationResource($place);
    }

    /**
     * Get place gallery images
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function gallery($id)
    {
        $place = Destination::where('id', $id)
            ->where('status', true)
            ->firstOrFail();

        $gallery = $place->gallery ?? [];

        return response()->json([
            'destination_id' => $place->id,
            'destination_name' => $place->name,
            'gallery' => array_map(function ($img) {
                return getImageUrl($img);
            }, $gallery),
        ]);
    }
}
