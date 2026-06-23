<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomItinerary;
use App\Models\B2CItinerary;
use App\Models\GroupItinerary;
use App\Models\Booking;
use App\Models\Agency;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Perform global search across all itinerary types, bookings, and agencies.
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return view('admin.search', [
                'q' => $q,
                'b2bResults' => collect(),
                'b2cResults' => collect(),
                'groupResults' => collect(),
                'bookingResults' => collect(),
                'agencyResults' => collect(),
                'totalCount' => 0
            ]);
        }

        // Clean query for ID matching
        $cleanQ = $q;
        $idQ = null;

        // Strip out common ID prefixes
        $prefixes = ['QT-', 'B2C-', 'GRP-', 'BK-'];
        foreach ($prefixes as $prefix) {
            if (stripos($cleanQ, $prefix) === 0) {
                $cleanQ = substr($cleanQ, strlen($prefix));
            }
        }

        // Handle Booking ID format (e.g., BK-YYYYMMDD-ID) or clean integer
        if (preg_match('/BK-\d+-(\d+)/i', $q, $matches)) {
            $idQ = intval($matches[1]);
        } elseif (is_numeric($cleanQ)) {
            $idQ = intval($cleanQ);
        }

        // 1. Query B2B Custom Itineraries
        $b2bQuery = CustomItinerary::with(['agency', 'destination']);
        if ($idQ) {
            $b2bQuery->where('id', $idQ);
        } else {
            $b2bQuery->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('client_name', 'like', "%{$q}%");
            });
        }
        $b2bResults = $b2bQuery->orderBy('updated_at', 'desc')->limit(15)->get();

        // 2. Query B2C Itineraries
        $b2cQuery = B2CItinerary::with(['destination']);
        if ($idQ) {
            $b2cQuery->where('id', $idQ);
        } else {
            $b2cQuery->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('client_name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            });
        }
        $b2cResults = $b2cQuery->orderBy('updated_at', 'desc')->limit(15)->get();

        // 3. Query Group Itineraries
        $groupQuery = GroupItinerary::with(['destination']);
        if ($idQ) {
            $groupQuery->where('id', $idQ);
        } else {
            $groupQuery->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('client_name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            });
        }
        $groupResults = $groupQuery->orderBy('updated_at', 'desc')->limit(15)->get();

        // 4. Query Bookings
        $bookingQuery = Booking::with(['package']);
        if ($idQ) {
            $bookingQuery->where('id', $idQ);
        } else {
            $bookingQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('customer_name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('customer_email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }
        $bookingResults = $bookingQuery->orderBy('updated_at', 'desc')->limit(15)->get();

        // 5. Query Agencies
        $agencyQuery = Agency::query();
        if ($idQ) {
            $agencyQuery->where('id', $idQ);
        } else {
            $agencyQuery->where(function ($query) use ($q) {
                $query->where('company_name', 'like', "%{$q}%")
                      ->orWhere('primary_contact_name', 'like', "%{$q}%")
                      ->orWhere('whatsapp_number', 'like', "%{$q}%");
            });
        }
        $agencyResults = $agencyQuery->orderBy('updated_at', 'desc')->limit(15)->get();

        $totalCount = $b2bResults->count() + $b2cResults->count() + $groupResults->count() + $bookingResults->count() + $agencyResults->count();

        return view('admin.search', compact(
            'q',
            'b2bResults',
            'b2cResults',
            'groupResults',
            'bookingResults',
            'agencyResults',
            'totalCount'
        ));
    }
}
