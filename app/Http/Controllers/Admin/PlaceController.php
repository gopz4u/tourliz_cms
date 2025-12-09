<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Place::withTrashed();

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $places = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json($places);
        }
        
        return view('admin.places.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.places.create');
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
            'region' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:places,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'price' => 'nullable|numeric|min:0',
            'rating' => 'nullable|integer|min:0|max:5',
            'featured' => 'nullable',
            'status' => 'nullable',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ], [
            'name.required' => 'The name field is required.',
            'region.required' => 'The region field is required.',
        ]);

        // Handle boolean fields - convert to boolean (accepts 0, 1, true, false, "0", "1", etc.)
        $validated['featured'] = isset($validated['featured']) && ($validated['featured'] == 1 || $validated['featured'] === true || $validated['featured'] === '1' || $validated['featured'] === 'true');
        $validated['status'] = isset($validated['status']) && ($validated['status'] == 1 || $validated['status'] === true || $validated['status'] === '1' || $validated['status'] === 'true') ? true : (isset($validated['status']) ? false : true);

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $place = Place::create($validated);

        return response()->json($place, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $place = Place::withTrashed()->findOrFail($id);
        return response()->json($place);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.places.edit', ['id' => $id]);
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
        $place = Place::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('places')->ignore($place->id)],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'price' => 'nullable|numeric|min:0',
            'rating' => 'nullable|integer|min:0|max:5',
            'featured' => 'nullable',
            'status' => 'nullable',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ], [
            'name.required' => 'The name field is required.',
            'region.required' => 'The region field is required.',
        ]);

        // Handle boolean fields - convert to boolean (accepts 0, 1, true, false, "0", "1", etc.)
        $validated['featured'] = isset($validated['featured']) && ($validated['featured'] == 1 || $validated['featured'] === true || $validated['featured'] === '1' || $validated['featured'] === 'true');
        $validated['status'] = isset($validated['status']) && ($validated['status'] == 1 || $validated['status'] === true || $validated['status'] === '1' || $validated['status'] === 'true') ? true : (isset($validated['status']) ? false : true);

        $place->update($validated);

        return response()->json($place);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        $place->delete();

        return response()->json(['message' => 'Place deleted successfully']);
    }
}
