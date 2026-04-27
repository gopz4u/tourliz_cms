<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class B2BBookingController extends Controller
{
    /**
     * List bookings for B2B consumers.
     * Supports filtering by status and pagination.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['package' => function ($q) {
            $q->withTrashed()->with('destination');
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderByDesc('updated_at')->paginate(
            min((int) $request->get('per_page', 20), 100)
        );

        return response()->json([
            'data' => $bookings->getCollection()->transform(function ($b) {
                return [
                    'id' => $b->id,
                    'package_id' => $b->package_id,
                    'package_name' => $b->package->name ?? null,
                    'place_name' => $b->package->destination->name ?? null,
                    'status' => $b->status,
                    'travel_date' => $b->travel_date,
                    'adults' => $b->adults,
                    'children' => $b->children,
                    'price' => $b->price,
                    'currency' => $b->currency,
                    'created_at' => $b->created_at,
                    'updated_at' => $b->updated_at,
                ];
            }),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }
}

