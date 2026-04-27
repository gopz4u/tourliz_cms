<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Destination::withTrashed();

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->has('country')) {
                $query->where('country', $request->country);
            }

            if ($request->has('location')) {
                $query->where('location', $request->location);
            }

            if ($request->has('city')) {
                $query->where('city', $request->city);
            }

            // If per_page is specified and is a large number, return all places (for dropdowns)
            $perPage = $request->get('per_page', 15);
            if ($perPage >= 1000) {
                $places = $query->orderBy('name', 'asc')->get();
                return response()->json(['data' => $places]);
            }

            $places = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json($places);
        }

        $destinations = Destination::orderBy('name', 'asc')->paginate(15);
        return view('admin.destinations.index', compact('destinations'));
    }

    /**
     * Get unique countries from destinations.
     */
    public function getCountries(): \Illuminate\Http\JsonResponse
    {
        $masterCountries = \App\Models\Country::where('status', true)
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->toArray();

        $destinationCountries = Destination::whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->pluck('country')
            ->toArray();

        // Merge, make unique, case-insensitive sort, and re-index array
        $countries = collect(array_merge($masterCountries, $destinationCountries))
            ->unique()
            ->sort(function ($a, $b) {
                return strcasecmp($a, $b);
            })
            ->values();

        return response()->json($countries);
    }

    /**
     * Get unique locations for a given country.
     */
    public function getLocations(Request $request): \Illuminate\Http\JsonResponse
    {
        $country = $request->get('country');
        $locations = Destination::where('country', $country)
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location', 'asc')
            ->pluck('location');
        return response()->json($locations);
    }

    /**
     * Get cities for a given country and location.
     */
    public function getCities(Request $request): \Illuminate\Http\JsonResponse
    {
        $country = $request->get('country');
        $location = $request->get('location');
        $cities = Destination::where('country', $country)
            ->where('location', $location)
            ->orderBy('city', 'asc')
            ->get(['id', 'city', 'name']);
        return response()->json($cities);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('admin.destinations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:destinations,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
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
            'name.required' => 'The display name field is required.',
            'country.required' => 'The country field is required.',
            'location.required' => 'The location field is required.',
            'city.required' => 'The city field is required.',
        ]);

        // Handle boolean fields - convert to boolean (accepts 0, 1, true, false, "0", "1", etc.)
        $validated['featured'] = isset($validated['featured']) && ($validated['featured'] == 1 || $validated['featured'] === true || $validated['featured'] === '1' || $validated['featured'] === 'true');
        $validated['status'] = isset($validated['status']) && ($validated['status'] == 1 || $validated['status'] === true || $validated['status'] === '1' || $validated['status'] === 'true') ? true : (isset($validated['status']) ? false : true);

        // Ensure gallery is not null
        $validated['gallery'] = $validated['gallery'] ?? [];

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $place = Destination::create($validated);

        return response()->json($place, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $destination = Destination::withTrashed()->findOrFail($id);
        return response()->json($destination);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id): \Illuminate\Contracts\View\View
    {
        return view('admin.destinations.edit', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $place = Destination::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('destinations')->ignore($place->id)],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
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
            'name.required' => 'The display name field is required.',
            'country.required' => 'The country field is required.',
            'location.required' => 'The location field is required.',
            'city.required' => 'The city field is required.',
        ]);

        // Handle boolean fields - convert to boolean (accepts 0, 1, true, false, "0", "1", etc.)
        $validated['featured'] = isset($validated['featured']) && ($validated['featured'] == 1 || $validated['featured'] === true || $validated['featured'] === '1' || $validated['featured'] === 'true');
        $validated['status'] = isset($validated['status']) && ($validated['status'] == 1 || $validated['status'] === true || $validated['status'] === '1' || $validated['status'] === 'true') ? true : (isset($validated['status']) ? false : true);

        // Ensure gallery is not null
        $validated['gallery'] = $validated['gallery'] ?? [];

        $place->update($validated);

        return response()->json($place);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $destination = Destination::withTrashed()->findOrFail($id);

            // Check if destination has associated packages
            $packageCount = \App\Models\Package::where('destination_id', $id)->count();

            // Use DB transaction to ensure atomicity
            DB::beginTransaction();

            try {
                // Set destination_id to null for all associated packages before deleting
                // The foreign key constraint should handle this automatically, but we do it manually for safety
                if ($packageCount > 0) {
                    \App\Models\Package::where('destination_id', $id)->update(['destination_id' => null]);
                }

                // Delete the destination
                if ($destination->trashed()) {
                    $destination->forceDelete();
                } else {
                    $destination->delete();
                }

                DB::commit();

                $message = 'Destination deleted successfully.';
                if ($packageCount > 0) {
                    $message .= " {$packageCount} associated packages had their reference removed.";
                }

                return redirect()->route('admin.destinations.index')->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            \Log::error('Database error deleting destination', [
                'destination_id' => $id,
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
                'sql_state' => $e->errorInfo[0] ?? null,
                'driver_code' => $e->errorInfo[1] ?? null,
                'sql_message' => $e->errorInfo[2] ?? null,
            ]);

            // Check if it's a foreign key constraint error
            if (
                strpos($errorMessage, 'foreign key constraint') !== false ||
                strpos($errorMessage, '1451') !== false ||
                strpos($errorMessage, '23000') !== false
            ) {

                return redirect()->route('admin.destinations.index')->with('error', 'Cannot delete destination because it is being used elsewhere in the system.');
            }

            return redirect()->route('admin.destinations.index')->with('error', 'Database error while deleting destination: ' . $errorMessage);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.destinations.index')->with('error', 'Destination not found.');

        } catch (\Exception $e) {
            \Log::error('Error deleting destination', [
                'destination_id' => $id,
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.destinations.index')->with('error', 'Error deleting destination: ' . $e->getMessage());
        }
    }
}
