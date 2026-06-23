<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Activity;
use App\Models\Transport;
use App\Models\EntryTicket;
use App\Models\Meal;
use App\Models\TouristSpot;
use App\Models\Service;
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

        $hotels = $query->get();

        // Query Service model for hotels
        $serviceQuery = Service::where('category', 'Hotels')->where('is_active', true);
        if ($destinationId) {
            $serviceQuery->where('destination_id', $destinationId);
        } elseif ($country) {
            $serviceQuery->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }
        if ($search) {
            $serviceQuery->where('name', 'like', "%{$search}%");
        }

        $services = $serviceQuery->get()->map(function($service) {
            return [
                'id' => $service->id,
                'name' => $service->name . ' (Core Service)',
                'star_rating' => $service->star_rating ?? 5,
                'is_core_service' => true,
                'accommodation_type' => $service->accommodation_type ?: 'Standard',
                'price' => $service->price,
                'currency' => $service->currency ?? 'MYR',
                'description' => $service->short_description ?? $service->description,
                'supplier_id' => $service->supplier_id,
                'rooms' => [
                    [
                        'id' => null,
                        'hotel_id' => null,
                        'room_type' => $service->accommodation_type ?: 'Standard',
                        'base_price' => $service->price,
                        'is_core_service' => true,
                        'service_id' => $service->id,
                    ]
                ]
            ];
        });

        return response()->json($hotels->concat($services)->values());
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

        $activities = $query->get();

        // Query Service model for activities
        $serviceQuery = Service::where('category', 'Activities')->where('is_active', true);
        if ($destinationId) {
            $serviceQuery->where('destination_id', $destinationId);
        } elseif ($country) {
            $serviceQuery->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }
        if ($search) {
            $serviceQuery->where('name', 'like', "%{$search}%");
        }

        $services = $serviceQuery->get()->map(function($service) {
            return [
                'id' => $service->id,
                'name' => $service->name . ' (Core Service)',
                'base_price' => $service->price,
                'child_price' => $service->price_2_6 ?: ($service->price * 0.5),
                'currency' => $service->currency ?? 'MYR',
                'description' => $service->short_description ?? $service->description,
                'supplier_id' => $service->supplier_id,
                'is_core_service' => true,
                'duration' => null
            ];
        });

        return response()->json($activities->concat($services)->values());
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

        $transports = $query->get();

        // Query Service model for transports
        $serviceQuery = Service::with('supplier')
            ->whereIn('category', ['Transport', 'Airport Pickup', 'Airport Drop'])
            ->where('is_active', true);

        if ($destination) {
            $serviceQuery->whereHas('destination', function($q) use ($destination) {
                $q->where('name', 'like', "%{$destination}%")
                  ->orWhere('city', 'like', "%{$destination}%")
                  ->orWhere('country', 'like', "%{$destination}%");
            });
        }

        if ($search) {
            $serviceQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('vehicle_type', 'like', "%{$search}%");
            });
        }

        $services = $serviceQuery->get()->map(function($service) {
            return [
                'id' => $service->id,
                'name' => $service->name . ' (Core Service)',
                'vehicle_type' => $service->vehicle_type ?: $service->name,
                'base_price' => $service->price,
                'price' => $service->price,
                'currency' => $service->currency ?? 'MYR',
                'description' => $service->short_description ?? $service->description,
                'supplier_id' => $service->supplier_id,
                'supplier' => $service->supplier,
                'is_core_service' => true,
                'capacity' => $service->total_pax ?: 'N/A'
            ];
        });

        return response()->json($transports->concat($services)->values());
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

        $tickets = $query->get();

        // Query Service model for tickets
        $serviceQuery = Service::where('category', 'Entry Tickets')->where('is_active', true);
        if ($destinationId) {
            $serviceQuery->where('destination_id', $destinationId);
        } elseif ($country) {
            $serviceQuery->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }
        if ($search) {
            $serviceQuery->where('name', 'like', "%{$search}%");
        }

        $services = $serviceQuery->get()->map(function($service) {
            return [
                'id' => $service->id,
                'attraction_name' => $service->name . ' (Core Service)',
                'adult_price' => $service->price,
                'child_price' => $service->price_2_6 ?: ($service->price * 0.5),
                'currency' => $service->currency ?? 'MYR',
                'description' => $service->short_description ?? $service->description,
                'supplier_id' => $service->supplier_id,
                'is_core_service' => true
            ];
        });

        return response()->json($tickets->concat($services)->values());
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

        $meals = $query->get();

        // Query Service model for meals
        $serviceQuery = Service::where('category', 'Meals')->where('is_active', true);
        if ($destinationId) {
            $serviceQuery->where(function ($q) use ($destinationId) {
                $q->where('destination_id', $destinationId)
                    ->orWhereNull('destination_id');
            });
        } elseif ($country) {
            $serviceQuery->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }
        if ($search) {
            $serviceQuery->where('name', 'like', "%{$search}%");
        }

        $services = $serviceQuery->get()->map(function($service) {
            return [
                'id' => $service->id,
                'name' => $service->name . ' (Core Service)',
                'price' => $service->price,
                'currency' => $service->currency ?? 'MYR',
                'type' => $service->accommodation_type ?: $service->vehicle_type ?: 'Meal',
                'description' => $service->short_description ?? $service->description,
                'supplier_id' => $service->supplier_id,
                'is_core_service' => true
            ];
        });

        return response()->json($meals->concat($services)->values());
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

        $spots = $query->get();

        // Query Service model for spots (Other Services category)
        $serviceQuery = Service::where('category', 'Other Services')->where('is_active', true);
        if ($destinationId) {
            $serviceQuery->where('destination_id', $destinationId);
        } elseif ($country) {
            $serviceQuery->whereHas('destination', function($q) use ($country) {
                $q->where('country', $country);
            });
        }
        if ($search) {
            $serviceQuery->where('name', 'like', "%{$search}%");
        }

        $services = $serviceQuery->get()->map(function($service) {
            return [
                'id' => $service->id,
                'name' => $service->name . ' (Core Service)',
                'description' => $service->short_description ?? $service->description,
                'is_core_service' => true,
                'supplier_id' => $service->supplier_id
            ];
        });

        return response()->json($spots->concat($services)->values());
    }

    public function supplierAssets(Request $request, $id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        $rawType = trim(strtolower($supplier->type));
        
        // Normalize type
        $type = $rawType;
        if (str_contains($rawType, 'hotel') || str_contains($rawType, 'accommodation')) {
            $type = 'hotel';
        } elseif (str_contains($rawType, 'transport')) {
            $type = 'transport';
        } elseif (str_contains($rawType, 'activity') || str_contains($rawType, 'ticket') || str_contains($rawType, 'attraction')) {
            $type = 'activity';
        } elseif (str_contains($rawType, 'meal')) {
            $type = 'meal';
        }

        $data = [
            'supplier' => $supplier,
            'type' => $type,
            'assets' => []
        ];

        // Fetch services for this supplier
        $services = Service::where('supplier_id', $id)->where('is_active', true)->get();

        switch ($type) {
            case 'hotel':
                $hotelServices = $services->filter(fn($s) => str_contains(strtolower($s->category), 'hotel'))->map(function($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name . ' (Core Service)',
                        'star_rating' => $service->star_rating ?? 5,
                        'is_core_service' => true,
                        'accommodation_type' => $service->accommodation_type ?: 'Standard',
                        'price' => $service->price,
                        'currency' => $service->currency ?? 'MYR',
                        'description' => $service->short_description ?? $service->description,
                        'supplier_id' => $service->supplier_id,
                        'rooms' => [
                            [
                                'id' => null,
                                'hotel_id' => null,
                                'room_type' => $service->accommodation_type ?: 'Standard',
                                'base_price' => $service->price,
                                'is_core_service' => true,
                                'service_id' => $service->id,
                            ]
                        ]
                    ];
                });
                $data['assets'] = \App\Models\Hotel::with('rooms')->where('supplier_id', $id)->get()->concat($hotelServices)->values();
                break;
            case 'transport':
                $transportServices = $services->filter(fn($s) => str_contains(strtolower($s->category), 'transport') || str_contains(strtolower($s->category), 'pickup') || str_contains(strtolower($s->category), 'drop'))->map(function($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name . ' (Core Service)',
                        'vehicle_type' => $service->vehicle_type ?: $service->name,
                        'base_price' => $service->price,
                        'price' => $service->price,
                        'currency' => $service->currency ?? 'MYR',
                        'description' => $service->short_description ?? $service->description,
                        'supplier_id' => $service->supplier_id,
                        'supplier' => $service->supplier,
                        'is_core_service' => true,
                        'capacity' => $service->total_pax ?: 'N/A'
                    ];
                });
                $data['assets'] = \App\Models\Transport::where('supplier_id', $id)->get()->concat($transportServices)->values();
                break;
            case 'activity':
                $activityServices = $services->filter(fn($s) => str_contains(strtolower($s->category), 'activity') || str_contains(strtolower($s->category), 'service'))->map(function($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name . ' (Core Service)',
                        'base_price' => $service->price,
                        'child_price' => $service->price_2_6 ?: ($service->price * 0.5),
                        'currency' => $service->currency ?? 'MYR',
                        'description' => $service->short_description ?? $service->description,
                        'supplier_id' => $service->supplier_id,
                        'is_core_service' => true,
                        'duration' => null
                    ];
                });
                $data['assets'] = \App\Models\Activity::where('supplier_id', $id)->get()->concat($activityServices)->values();
                
                $ticketServices = $services->filter(fn($s) => str_contains(strtolower($s->category), 'ticket'))->map(function($service) {
                    return [
                        'id' => $service->id,
                        'attraction_name' => $service->name . ' (Core Service)',
                        'adult_price' => $service->price,
                        'child_price' => $service->price_2_6 ?: ($service->price * 0.5),
                        'currency' => $service->currency ?? 'MYR',
                        'description' => $service->short_description ?? $service->description,
                        'supplier_id' => $service->supplier_id,
                        'is_core_service' => true
                    ];
                });
                $data['extra_assets'] = \App\Models\EntryTicket::where('supplier_id', $id)->get()->concat($ticketServices)->values();
                break;
            case 'meal':
                $mealServices = $services->filter(fn($s) => str_contains(strtolower($s->category), 'meal'))->map(function($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name . ' (Core Service)',
                        'price' => $service->price,
                        'currency' => $service->currency ?? 'MYR',
                        'type' => $service->accommodation_type ?: $service->vehicle_type ?: 'Meal',
                        'description' => $service->short_description ?? $service->description,
                        'supplier_id' => $service->supplier_id,
                        'is_core_service' => true
                    ];
                });
                $data['assets'] = \App\Models\Meal::where('supplier_id', $id)->get()->concat($mealServices)->values();
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
                
                $mappedServices = $services->map(function($service) {
                    $cat = strtolower($service->category);
                    if (str_contains($cat, 'hotel')) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name . ' (Core Service)',
                            'star_rating' => $service->star_rating ?? 5,
                            'is_core_service' => true,
                            'rooms' => [
                                [
                                    'id' => null,
                                    'hotel_id' => null,
                                    'room_type' => $service->accommodation_type ?: 'Standard',
                                    'base_price' => $service->price,
                                    'is_core_service' => true,
                                    'service_id' => $service->id,
                                ]
                            ]
                        ];
                    }
                    return [
                        'id' => $service->id,
                        'name' => $service->name . ' (Core Service)',
                        'is_core_service' => true,
                        'price' => $service->price,
                        'base_price' => $service->price,
                        'currency' => $service->currency ?? 'MYR',
                        'description' => $service->short_description ?? $service->description
                    ];
                });

                $data['assets'] = $allAssets->concat($mappedServices)->values();
        }

        return response()->json($data);
    }
}
