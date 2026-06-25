<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    public function hotels(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = Service::where('category', 'Hotels')->where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function ($q) use ($country) {
                $q->where('country', $country);
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $results = $query->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
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

        return response()->json($results->values());
    }

    public function activities(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = Service::where('category', 'Activities')->where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function ($q) use ($country) {
                $q->where('country', $country);
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $results = $query->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'base_price' => $service->price,
                'adult_price' => $service->price,
                'child_price' => $service->price_2_6 ?: ($service->price * 0.5),
                'child_2_6_price' => $service->price_2_6 ?: ($service->price * 0.5),
                'child_6_11_price' => $service->price_6_10 ?: ($service->price * 0.75),
                'currency' => $service->currency ?? 'MYR',
                'description' => $service->short_description ?? $service->description,
                'supplier_id' => $service->supplier_id,
                'is_core_service' => true,
                'duration' => null,
            ];
        });

        return response()->json($results->values());
    }

    public function transports(Request $request)
    {
        $search = $request->query('search');
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $destination = $request->query('destination');

        $query = Service::with('supplier')
            ->whereIn('category', ['Transport', 'Airport Pickup', 'Airport Drop'])
            ->where('is_active', true);

        if ($destinationId) {
            $query->where(function ($q) use ($destinationId) {
                $q->where('destination_id', $destinationId)
                    ->orWhereNull('destination_id');
            });
        } elseif ($country) {
            $query->where(function ($q) use ($country) {
                $q->whereHas('destination', function ($innerQ) use ($country) {
                    $innerQ->where('country', $country);
                })->orWhereNull('destination_id');
            });
        } elseif ($destination) {
            $query->where(function ($q) use ($destination) {
                $q->whereHas('destination', function ($innerQ) use ($destination) {
                    $innerQ->where('name', 'like', "%{$destination}%")
                        ->orWhere('city', 'like', "%{$destination}%")
                        ->orWhere('country', 'like', "%{$destination}%");
                })->orWhereNull('destination_id');
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('vehicle_type', 'like', "%{$search}%");
            });
        }

        $results = $query->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'vehicle_type' => $service->vehicle_type ?: $service->name,
                'base_price' => $service->price,
                'price' => $service->price,
                'currency' => $service->currency ?? 'MYR',
                'description' => $service->short_description ?? $service->description,
                'supplier_id' => $service->supplier_id,
                'supplier' => $service->supplier,
                'is_core_service' => true,
                'capacity' => $service->total_pax ?: 'N/A',
            ];
        });

        return response()->json($results->values());
    }

    public function tickets(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = Service::where('category', 'Entry Tickets')->where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function ($q) use ($country) {
                $q->where('country', $country);
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $results = $query->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'attraction_name' => $service->name,
                'adult_price' => $service->price,
                'child_price' => $service->price_2_6 ?: ($service->price * 0.5),
                'child_2_6_price' => $service->price_2_6 ?: ($service->price * 0.5),
                'child_6_11_price' => $service->price_6_10 ?: ($service->price * 0.75),
                'currency' => $service->currency ?? 'MYR',
                'description' => $service->short_description ?? $service->description,
                'supplier_id' => $service->supplier_id,
                'is_core_service' => true,
            ];
        });

        return response()->json($results->values());
    }

    public function meals(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = Service::where('category', 'Meals')->where('is_active', true);

        if ($destinationId) {
            $query->where(function ($q) use ($destinationId) {
                $q->where('destination_id', $destinationId)
                    ->orWhereNull('destination_id');
            });
        } elseif ($country) {
            $query->whereHas('destination', function ($q) use ($country) {
                $q->where('country', $country);
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $results = $query->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'currency' => $service->currency ?? 'MYR',
                'type' => $service->accommodation_type ?: $service->vehicle_type ?: 'Meal',
                'description' => $service->short_description ?? $service->description,
                'supplier_id' => $service->supplier_id,
                'is_core_service' => true,
            ];
        });

        return response()->json($results->values());
    }

    public function spots(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $countryId     = $request->query('country_id');
        $country       = $request->query('country');   // country name string fallback
        $search        = $request->query('search');

        $query = \App\Models\TouristSpot::with(['destination', 'country'])
            ->where('is_active', true);

        // Primary filter: country_id (direct FK — most accurate)
        if ($countryId) {
            $query->where('country_id', $countryId);
        } elseif ($country) {
            // Fallback: match by country name string
            $query->whereHas('country', function ($q) use ($country) {
                $q->where('name', $country);
            })->orWhereHas('destination', function ($q) use ($country) {
                $q->where('country', $country);
            });
        }

        // Optional: further narrow by specific city/destination
        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $results = $query->orderBy('name')->get()->map(function ($ts) {
            return [
                'id'             => $ts->id,
                'name'           => $ts->name,
                'description'    => $ts->description,
                'image_url'      => $ts->image_url,
                'supplier_id'    => $ts->supplier_id,
                'country_id'     => $ts->country_id,
                'country_name'   => $ts->country->name ?? '',
                'destination_id' => $ts->destination_id,
                'destination'    => $ts->destination->name ?? '',
                'price'          => 0,
                'price_per_hour' => 0,
                'is_core_service'=> false,
            ];
        });

        return response()->json($results->values());
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
            'assets' => [],
        ];

        // Fetch services for this supplier
        $services = Service::where('supplier_id', $id)->where('is_active', true)->get();

        switch ($type) {
            case 'hotel':
                $data['assets'] = $services->filter(fn($s) => str_contains(strtolower($s->category), 'hotel'))->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
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
                })->values();
                break;

            case 'transport':
                $data['assets'] = $services->filter(fn($s) => str_contains(strtolower($s->category), 'transport') || str_contains(strtolower($s->category), 'pickup') || str_contains(strtolower($s->category), 'drop'))->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'vehicle_type' => $service->vehicle_type ?: $service->name,
                        'base_price' => $service->price,
                        'price' => $service->price,
                        'currency' => $service->currency ?? 'MYR',
                        'description' => $service->short_description ?? $service->description,
                        'supplier_id' => $service->supplier_id,
                        'supplier' => $service->supplier,
                        'is_core_service' => true,
                        'capacity' => $service->total_pax ?: 'N/A',
                    ];
                })->values();
                break;

            case 'activity':
                $data['assets'] = $services->filter(fn($s) => str_contains(strtolower($s->category), 'activity') || str_contains(strtolower($s->category), 'service'))->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'base_price' => $service->price,
                        'adult_price' => $service->price,
                        'child_price' => $service->price_2_6 ?: ($service->price * 0.5),
                        'child_2_6_price' => $service->price_2_6 ?: ($service->price * 0.5),
                        'child_6_11_price' => $service->price_6_10 ?: ($service->price * 0.75),
                        'currency' => $service->currency ?? 'MYR',
                        'description' => $service->short_description ?? $service->description,
                        'supplier_id' => $service->supplier_id,
                        'is_core_service' => true,
                        'duration' => null,
                    ];
                })->values();

                $data['extra_assets'] = $services->filter(fn($s) => str_contains(strtolower($s->category), 'ticket'))->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'attraction_name' => $service->name,
                        'adult_price' => $service->price,
                        'child_price' => $service->price_2_6 ?: ($service->price * 0.5),
                        'child_2_6_price' => $service->price_2_6 ?: ($service->price * 0.5),
                        'child_6_11_price' => $service->price_6_10 ?: ($service->price * 0.75),
                        'currency' => $service->currency ?? 'MYR',
                        'description' => $service->short_description ?? $service->description,
                        'supplier_id' => $service->supplier_id,
                        'is_core_service' => true,
                    ];
                })->values();
                break;

            case 'meal':
                $data['assets'] = $services->filter(fn($s) => str_contains(strtolower($s->category), 'meal'))->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'currency' => $service->currency ?? 'MYR',
                        'type' => $service->accommodation_type ?: $service->vehicle_type ?: 'Meal',
                        'description' => $service->short_description ?? $service->description,
                        'supplier_id' => $service->supplier_id,
                        'is_core_service' => true,
                    ];
                })->values();
                break;

            default:
                $data['assets'] = $services->map(function ($service) {
                    $cat = strtolower($service->category);
                    if (str_contains($cat, 'hotel')) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
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
                        'name' => $service->name,
                        'is_core_service' => true,
                        'price' => $service->price,
                        'base_price' => $service->price,
                        'currency' => $service->currency ?? 'MYR',
                        'description' => $service->short_description ?? $service->description,
                    ];
                })->values();
        }

        return response()->json($data);
    }
}
