<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\Destination;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AttractionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            try {
                $query = Attraction::withTrashed()->with([
                    'destination' => function ($q) {
                        $q->withTrashed();
                    },
                    'package' => function ($q) {
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

                // Filter by package if provided
                if ($request->has('package_id') && $request->package_id) {
                    $query->where('package_id', $request->package_id);
                }

                $attractions = $query->orderBy('created_at', 'desc')->paginate(15);

                return response()->json($attractions);
            } catch (\Exception $e) {
                \Log::error('Error loading attractions: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Error loading attractions',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        return view('admin.attractions.index');
    }

    public function create()
    {
        return view('admin.attractions.create');
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:attractions,slug',
            'price' => 'nullable|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'price_2_6' => 'nullable|numeric|min:0',
            'price_6_10' => 'nullable|numeric|min:0',
            'announcement_date' => 'nullable|date',
            'total_pax' => 'nullable|integer|min:1',
            'currency' => 'nullable|string|in:INR,USD,MYR,SGD,AED',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'destination_id' => 'nullable|exists:destinations,id',
            'package_id' => 'nullable|exists:packages,id',
            'is_active' => 'nullable',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle boolean fields
        $validated['status'] = isset($validated['is_active']) && ($validated['is_active'] == 1 || $validated['is_active'] === true || $validated['is_active'] === '1' || $validated['is_active'] === 'true') ? true : (isset($validated['is_active']) ? false : true);
        unset($validated['is_active']);

        // Set default currency if not provided
        if (!isset($validated['currency'])) {
            $validated['currency'] = 'MYR';
        }

        // Ensure gallery is not null
        $validated['gallery'] = $validated['gallery'] ?? [];

        $attraction = Attraction::create($validated);

        return response()->json($attraction, 201);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $attraction = Attraction::withTrashed()->with([
                'destination' => function ($q) {
                    $q->withTrashed();
                },
                'package' => function ($q) {
                    $q->withTrashed();
                }
            ])->findOrFail($id);
            return response()->json($attraction);
        } catch (\Exception $e) {
            \Log::error('Error loading attraction: ' . $e->getMessage());
            return response()->json([
                'error' => 'Attraction not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function edit($id)
    {
        return view('admin.attractions.edit', ['id' => $id]);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try {
            $attraction = Attraction::withTrashed()->findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => ['nullable', 'string', 'max:255', Rule::unique('attractions')->ignore($attraction->id)],
                'price' => 'nullable|numeric|min:0',
                'offer_price' => 'nullable|numeric|min:0',
                'image' => 'nullable|string',
                'gallery' => 'nullable|array',
                'short_description' => 'nullable|string',
                'description' => 'nullable|string',
                'destination_id' => 'nullable|exists:destinations,id',
                'package_id' => 'nullable|exists:packages,id',
                'is_active' => 'nullable',
                'meta_title' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
            ]);

            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Handle boolean fields
            $validated['status'] = isset($validated['is_active']) && ($validated['is_active'] == 1 || $validated['is_active'] === true || $validated['is_active'] === '1' || $validated['is_active'] === 'true') ? true : (isset($validated['is_active']) ? false : true);
            unset($validated['is_active']);

            // Ensure gallery is not null
            $validated['gallery'] = $validated['gallery'] ?? [];

            $attraction->update($validated);

            return response()->json($attraction);
        } catch (\Exception $e) {
            \Log::error('Error updating attraction: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error updating attraction',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $attraction = Attraction::withTrashed()->findOrFail($id);

            // If already soft deleted, force delete
            if ($attraction->trashed()) {
                $attraction->forceDelete();
            } else {
                $attraction->delete();
            }

            DB::commit();

            return response()->json(['message' => 'Attraction deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            \Log::error('Attraction not found for deletion: ' . $e->getMessage());
            return response()->json([
                'error' => 'Attraction not found',
                'message' => 'The attraction you are trying to delete does not exist.'
            ], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('Database error while deleting attraction: ' . $e->getMessage(), [
                'attraction_id' => $id,
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);

            // Check if it's a foreign key constraint violation
            if ($e->getCode() == 23000) {
                return response()->json([
                    'error' => 'Cannot delete attraction',
                    'message' => 'This attraction is being used by other records and cannot be deleted.'
                ], 409);
            }

            return response()->json([
                'error' => 'Database error while deleting attraction',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting attraction: ' . $e->getMessage(), [
                'attraction_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error deleting attraction',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
