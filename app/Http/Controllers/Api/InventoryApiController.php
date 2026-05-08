<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Activity;
use App\Models\Transport;
use App\Models\EntryTicket;
use App\Models\Meal;
use App\Models\TouristSpot;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    public function hotels(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = Hotel::with('rooms')->where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return response()->json($query->get());
    }

    public function activities(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = Activity::where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return response()->json($query->get());
    }

    public function transports(Request $request)
    {
        $search = $request->query('search');
        $destination = $request->query('destination'); // Note: Transport uses string destination

        $query = Transport::with('supplier')->where('is_active', true);

        if ($destination) {
            $query->where('destination', $destination);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('vehicle_type', 'like', "%{$search}%");
            });
        }

        return response()->json($query->get());
    }

    public function tickets(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = EntryTicket::where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }

        if ($search) {
            $query->where('attraction_name', 'like', "%{$search}%");
        }

        return response()->json($query->get());
    }

    public function meals(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = Meal::where('is_active', true);

        if ($destinationId) {
            $query->where(function ($q) use ($destinationId) {
                $q->where('destination_id', $destinationId)
                    ->orWhereNull('destination_id');
            });
        } elseif ($country) {
            $query->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return response()->json($query->get());
    }

    public function spots(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = TouristSpot::where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return response()->json($query->get());
    }

    public function supplierAssets(Request $request, $id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        $type = strtolower($supplier->type);

        $data = [
            'supplier' => $supplier,
            'type' => $type,
            'assets' => []
        ];

        switch ($type) {
            case 'hotel':
            case 'hotels':
            case 'accommodation':
                $data['assets'] = \App\Models\Hotel::with('rooms')->where('supplier_id', $id)->get();
                $data['type'] = 'hotel'; // Normalize for frontend
                break;
            case 'transport':
            case 'transports':
                $data['assets'] = \App\Models\Transport::where('supplier_id', $id)->get();
                break;
            case 'activity':
            case 'activities':
            case 'agent':
            case 'tickets':
                $data['assets'] = \App\Models\Activity::where('supplier_id', $id)->get();
                // Also load entry tickets if they exist for this supplier
                $data['extra_assets'] = \App\Models\EntryTicket::where('supplier_id', $id)->get();
                break;
            case 'meal':
            case 'meals':
                $data['assets'] = \App\Models\Meal::where('supplier_id', $id)->get();
                break;
            default:
                // Try to find anything linked to this supplier
                $allAssets = collect();
                $allAssets = $allAssets->merge(\App\Models\Hotel::with('rooms')->where('supplier_id', $id)->get());
                $allAssets = $allAssets->merge(\App\Models\Transport::where('supplier_id', $id)->get());
                $allAssets = $allAssets->merge(\App\Models\Activity::where('supplier_id', $id)->get());
                if (class_exists(\App\Models\EntryTicket::class)) {
                    $allAssets = $allAssets->merge(\App\Models\EntryTicket::where('supplier_id', $id)->get());
                }
                $allAssets = $allAssets->merge(\App\Models\Meal::where('supplier_id', $id)->get());
                
                $data['assets'] = $allAssets->values();
                break;
        }

        return response()->json($data);
    }
}
