<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Display a listing of places.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Place::where('status', true);

        // Filter by region
        if ($request->has('region')) {
            $query->where('region', $request->region);
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

        return PlaceResource::collection($places);
    }

    /**
     * Display the specified place.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $place = Place::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return new PlaceResource($place);
    }
}
