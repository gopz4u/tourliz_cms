<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Country;
use App\Models\Destination;
use App\Models\EntryTicket;
use App\Models\GroupPackage;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Meal;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\TouristSpot;
use App\Models\Transport;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    /**
     * Get Hotels from Operations master
     */
    public function hotels(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $countryId = $request->query('country_id');
        $search = $request->query('search');

        $query = Hotel::with(['rooms', 'destination' => function ($q) {
            $q->withTrashed();
        }, 'supplier'])->where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($countryId) {
            $countryModel = Country::find($countryId);
            $countryName = $countryModel ? $countryModel->name : null;
            $query->where(function ($q) use ($countryId, $countryName) {
                $q->where('destination_id', $countryId);
                if ($countryName) {
                    $q->orWhereHas('destination', function ($innerQ) use ($countryName) {
                        $innerQ->withTrashed()->where(function ($d) use ($countryName) {
                            $d->where('country', $countryName)->orWhere('name', $countryName);
                        });
                    });
                }
            });
        } elseif ($country) {
            $query->whereHas('destination', function ($q) use ($country) {
                $q->withTrashed()->where(function ($d) use ($country) {
                    $d->where('country', $country)->orWhere('name', $country);
                });
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $hotels = $query->orderBy('name')->get();

        // If operational hotels exist, map them
        if ($hotels->isNotEmpty()) {
            $results = $hotels->map(function ($hotel) {
                $rooms = $hotel->rooms->map(function ($room) use ($hotel) {
                    return [
                        'id' => $room->id,
                        'hotel_id' => $hotel->id,
                        'hotel_room_id' => $room->id,
                        'room_type' => $room->room_type,
                        'base_price' => (float)$room->base_price,
                        'price' => (float)$room->base_price,
                        'currency' => $room->currency ?? $hotel->currency ?? 'MYR',
                        'capacity' => $room->capacity ?? 2,
                        'is_core_service' => false,
                    ];
                });

                if ($rooms->isEmpty()) {
                    $rooms = collect([[
                        'id' => null,
                        'hotel_id' => $hotel->id,
                        'hotel_room_id' => null,
                        'room_type' => 'Standard Room',
                        'base_price' => 0.00,
                        'price' => 0.00,
                        'currency' => $hotel->currency ?? 'MYR',
                        'capacity' => 2,
                        'is_core_service' => false,
                    ]]);
                }

                $firstRoom = $rooms->first();

                return [
                    'id' => $hotel->id,
                    'hotel_id' => $hotel->id,
                    'name' => $hotel->name,
                    'star_rating' => $hotel->star_rating ?? 4,
                    'is_core_service' => false,
                    'accommodation_type' => 'Hotel',
                    'price' => $firstRoom ? $firstRoom['base_price'] : 0,
                    'currency' => $hotel->currency ?? 'MYR',
                    'description' => $hotel->description,
                    'address' => $hotel->address,
                    'supplier_id' => $hotel->supplier_id,
                    'supplier' => $hotel->supplier ? ['id' => $hotel->supplier->id, 'name' => $hotel->supplier->name] : null,
                    'rooms' => $rooms->values()->all(),
                ];
            });

            return response()->json($results->values());
        }

        // Fallback to legacy Service table if no operational hotels found
        $serviceQuery = Service::where('category', 'Hotels')->where('is_active', true);
        if ($destinationId) {
            $serviceQuery->where('destination_id', $destinationId);
        } elseif ($country) {
            $serviceQuery->whereHas('destination', function ($q) use ($country) {
                $q->withTrashed()->where('country', $country);
            });
        }
        if ($search) {
            $serviceQuery->where('name', 'like', "%{$search}%");
        }

        $results = $serviceQuery->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'hotel_id' => null,
                'name' => $service->name,
                'star_rating' => $service->star_rating ?? 4,
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
                        'hotel_room_id' => null,
                        'room_type' => $service->accommodation_type ?: 'Standard',
                        'base_price' => $service->price,
                        'price' => $service->price,
                        'currency' => $service->currency ?? 'MYR',
                        'capacity' => 2,
                        'is_core_service' => true,
                        'service_id' => $service->id,
                    ]
                ]
            ];
        });

        return response()->json($results->values());
    }

    /**
     * Get Activities from Operations master
     */
    public function activities(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = Activity::with(['destination' => function ($q) {
            $q->withTrashed();
        }, 'supplier'])->where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function ($q) use ($country) {
                $q->withTrashed()->where(function ($d) use ($country) {
                    $d->where('country', $country)->orWhere('name', $country);
                });
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $activities = $query->orderBy('name')->get();

        if ($activities->isNotEmpty()) {
            $results = $activities->map(function ($act) {
                $basePrice = (float)$act->base_price;
                return [
                    'id' => $act->id,
                    'activity_id' => $act->id,
                    'name' => $act->name,
                    'base_price' => $basePrice,
                    'adult_price' => $basePrice,
                    'child_price' => (float)($basePrice * 0.7),
                    'child_2_6_price' => (float)($basePrice * 0.5),
                    'child_6_11_price' => (float)($basePrice * 0.75),
                    'currency' => 'MYR',
                    'description' => $act->description,
                    'duration' => $act->duration,
                    'supplier_id' => $act->supplier_id,
                    'supplier' => $act->supplier ? ['id' => $act->supplier->id, 'name' => $act->supplier->name] : null,
                    'is_core_service' => false,
                ];
            });

            return response()->json($results->values());
        }

        // Fallback to Service table
        $serviceQuery = Service::where('category', 'Activities')->where('is_active', true);
        if ($destinationId) {
            $serviceQuery->where('destination_id', $destinationId);
        } elseif ($country) {
            $serviceQuery->whereHas('destination', function ($q) use ($country) {
                $q->withTrashed()->where('country', $country);
            });
        }
        if ($search) {
            $serviceQuery->where('name', 'like', "%{$search}%");
        }

        $results = $serviceQuery->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'activity_id' => null,
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

    /**
     * Get Transports from Operations master
     */
    public function transports(Request $request)
    {
        $search = $request->query('search');
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $destination = $request->query('destination');

        $query = Transport::with(['destination' => function ($q) {
            $q->withTrashed();
        }, 'supplier'])->where('is_active', true);

        if ($destinationId) {
            $query->where(function ($q) use ($destinationId) {
                $q->where('destination_id', $destinationId)->orWhereNull('destination_id');
            });
        } elseif ($country) {
            $query->where(function ($q) use ($country) {
                $q->whereHas('destination', function ($innerQ) use ($country) {
                    $innerQ->withTrashed()->where(function ($d) use ($country) {
                        $d->where('country', $country)->orWhere('name', $country);
                    });
                })->orWhereNull('destination_id');
            });
        } elseif ($destination) {
            $query->where(function ($q) use ($destination) {
                $q->whereHas('destination', function ($innerQ) use ($destination) {
                    $innerQ->withTrashed()->where(function ($d) use ($destination) {
                        $d->where('name', 'like', "%{$destination}%")
                            ->orWhere('city', 'like', "%{$destination}%")
                            ->orWhere('country', 'like', "%{$destination}%");
                    });
                })->orWhereNull('destination_id');
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('vehicle_type', 'like', "%{$search}%");
            });
        }

        $transports = $query->orderBy('name')->get();

        if ($transports->isNotEmpty()) {
            $results = $transports->map(function ($transport) {
                return [
                    'id' => $transport->id,
                    'transport_id' => $transport->id,
                    'name' => $transport->name,
                    'vehicle_type' => $transport->vehicle_type ?: $transport->name,
                    'base_price' => (float)$transport->base_price,
                    'price' => (float)$transport->base_price,
                    'currency' => $transport->currency ?? 'MYR',
                    'description' => $transport->destination ? ('Transfer to ' . $transport->destination) : '',
                    'duration' => $transport->duration ?: ($transport->name ?? 'Transfer'),
                    'capacity' => $transport->capacity ?: 'N/A',
                    'supplier_id' => $transport->supplier_id,
                    'supplier' => $transport->supplier ? ['id' => $transport->supplier->id, 'name' => $transport->supplier->name] : null,
                    'is_core_service' => false,
                ];
            });

            return response()->json($results->values());
        }

        // Fallback to Service table
        $serviceQuery = Service::with('supplier')
            ->whereIn('category', ['Transport', 'Airport Pickup', 'Airport Drop'])
            ->where('is_active', true);

        if ($destinationId) {
            $serviceQuery->where(function ($q) use ($destinationId) {
                $q->where('destination_id', $destinationId)->orWhereNull('destination_id');
            });
        } elseif ($country) {
            $serviceQuery->where(function ($q) use ($country) {
                $q->whereHas('destination', function ($innerQ) use ($country) {
                    $innerQ->withTrashed()->where('country', $country);
                })->orWhereNull('destination_id');
            });
        }

        if ($search) {
            $serviceQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('vehicle_type', 'like', "%{$search}%");
            });
        }

        $results = $serviceQuery->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'transport_id' => null,
                'name' => $service->name,
                'vehicle_type' => $service->vehicle_type ?: $service->name,
                'base_price' => $service->price,
                'price' => $service->price,
                'currency' => $service->currency ?? 'MYR',
                'description' => $service->short_description ?? $service->description,
                'duration' => $service->name,
                'supplier_id' => $service->supplier_id,
                'supplier' => $service->supplier,
                'is_core_service' => true,
                'capacity' => $service->total_pax ?: 'N/A',
            ];
        });

        return response()->json($results->values());
    }

    /**
     * Get Entry Tickets from Operations master
     */
    public function tickets(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = EntryTicket::with(['destination' => function ($q) {
            $q->withTrashed();
        }, 'supplier'])->where('is_active', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function ($q) use ($country) {
                $q->withTrashed()->where(function ($d) use ($country) {
                    $d->where('country', $country)->orWhere('name', $country);
                });
            });
        }

        if ($search) {
            $query->where('attraction_name', 'like', "%{$search}%");
        }

        $tickets = $query->orderBy('attraction_name')->get();

        if ($tickets->isNotEmpty()) {
            $results = $tickets->map(function ($ticket) {
                $adultPrice = (float)$ticket->adult_price;
                $childPrice = (float)($ticket->child_price ?: ($adultPrice * 0.7));
                return [
                    'id' => $ticket->id,
                    'ticket_id' => $ticket->id,
                    'attraction_name' => $ticket->attraction_name,
                    'name' => $ticket->attraction_name,
                    'adult_price' => $adultPrice,
                    'child_price' => $childPrice,
                    'child_2_6_price' => (float)($childPrice * 0.7),
                    'child_6_11_price' => $childPrice,
                    'currency' => $ticket->currency ?? 'MYR',
                    'description' => 'Entry Ticket: ' . $ticket->attraction_name,
                    'supplier_id' => $ticket->supplier_id,
                    'supplier' => $ticket->supplier ? ['id' => $ticket->supplier->id, 'name' => $ticket->supplier->name] : null,
                    'is_core_service' => false,
                ];
            });

            return response()->json($results->values());
        }

        // Fallback to Service table
        $serviceQuery = Service::where('category', 'Entry Tickets')->where('is_active', true);
        if ($destinationId) {
            $serviceQuery->where('destination_id', $destinationId);
        } elseif ($country) {
            $serviceQuery->whereHas('destination', function ($q) use ($country) {
                $q->withTrashed()->where('country', $country);
            });
        }
        if ($search) {
            $serviceQuery->where('name', 'like', "%{$search}%");
        }

        $results = $serviceQuery->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'ticket_id' => null,
                'attraction_name' => $service->name,
                'name' => $service->name,
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

    /**
     * Get Meals from Operations master
     */
    public function meals(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = Meal::with(['destination' => function ($q) {
            $q->withTrashed();
        }, 'supplier'])->where('is_active', true);

        if ($destinationId) {
            $query->where(function ($q) use ($destinationId) {
                $q->where('destination_id', $destinationId)->orWhereNull('destination_id');
            });
        } elseif ($country) {
            $query->where(function ($q) use ($country) {
                $q->whereHas('destination', function ($q2) use ($country) {
                    $q2->withTrashed()->where(function ($d) use ($country) {
                        $d->where('country', $country)->orWhere('name', $country);
                    });
                })->orWhereNull('destination_id');
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $meals = $query->orderBy('name')->get();

        if ($meals->isNotEmpty()) {
            $results = $meals->map(function ($meal) {
                return [
                    'id' => $meal->id,
                    'meal_id' => $meal->id,
                    'name' => $meal->name,
                    'price' => (float)$meal->price,
                    'currency' => 'MYR',
                    'type' => $meal->type ?: 'Meal',
                    'description' => $meal->description ?? '',
                    'supplier_id' => $meal->supplier_id,
                    'supplier' => $meal->supplier ? ['id' => $meal->supplier->id, 'name' => $meal->supplier->name] : null,
                    'is_core_service' => false,
                ];
            });

            return response()->json($results->values());
        }

        // Fallback to Service table
        $serviceQuery = Service::where('category', 'Meals')->where('is_active', true);
        if ($destinationId) {
            $serviceQuery->where(function ($q) use ($destinationId) {
                $q->where('destination_id', $destinationId)->orWhereNull('destination_id');
            });
        } elseif ($country) {
            $serviceQuery->whereHas('destination', function ($q) use ($country) {
                $q->withTrashed()->where('country', $country);
            });
        }
        if ($search) {
            $serviceQuery->where('name', 'like', "%{$search}%");
        }

        $results = $serviceQuery->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'meal_id' => null,
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

    /**
     * Get Tourist Spots from Operations master
     */
    public function spots(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $countryId     = $request->query('country_id');
        $country       = $request->query('country');
        $search        = $request->query('search');

        $query = TouristSpot::with(['destination' => function ($q) {
            $q->withTrashed();
        }, 'country', 'supplier'])->where('is_active', true);

        // Filter by country_id or country name
        if ($countryId) {
            $countryModel = Country::find($countryId);
            $countryName = $countryModel ? $countryModel->name : null;
            $query->where(function ($q) use ($countryId, $countryName) {
                $q->where('country_id', $countryId);
                if ($countryName) {
                    $q->orWhereHas('destination', function ($sub) use ($countryName) {
                        $sub->withTrashed()->where(function ($d) use ($countryName) {
                            $d->where('country', $countryName)->orWhere('name', $countryName);
                        });
                    });
                }
            });
        } elseif ($country) {
            $query->where(function ($q) use ($country) {
                $q->whereHas('country', function ($sub) use ($country) {
                    $sub->where('name', $country);
                })->orWhereHas('destination', function ($sub) use ($country) {
                    $sub->withTrashed()->where(function ($d) use ($country) {
                        $d->where('country', $country)->orWhere('name', $country);
                    });
                });
            });
        }

        // Further narrow by specific city/destination if provided
        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $results = $query->orderBy('name')->get()->map(function ($ts) {
            return [
                'id'             => $ts->id,
                'spot_id'        => $ts->id,
                'name'           => $ts->name,
                'description'    => $ts->description,
                'image_url'      => $ts->image_url,
                'supplier_id'    => $ts->supplier_id,
                'country_id'     => $ts->country_id,
                'country_name'   => $ts->country->name ?? ($ts->destination->country ?? ''),
                'destination_id' => $ts->destination_id,
                'destination'    => $ts->destination->name ?? '',
                'price'          => 0,
                'price_per_hour' => 0,
                'is_core_service'=> false,
            ];
        });

        return response()->json($results->values());
    }

    /**
     * Get Group Packages (Master Group Tours) from Operations
     */
    public function groupPackages(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $country = $request->query('country');
        $search = $request->query('search');

        $query = GroupPackage::with(['destination' => function ($q) {
            $q->withTrashed();
        }, 'supplier'])->where('status', true);

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        } elseif ($country) {
            $query->whereHas('destination', function ($q) use ($country) {
                $q->withTrashed()->where(function ($d) use ($country) {
                    $d->where('country', $country)->orWhere('name', $country);
                });
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $results = $query->orderBy('name')->get()->map(function ($gp) {
            $itineraryData = is_array($gp->itinerary) ? $gp->itinerary : (json_decode($gp->itinerary, true) ?: []);
            return [
                'id' => $gp->id,
                'name' => $gp->name,
                'slug' => $gp->slug,
                'price' => (float)$gp->price,
                'currency' => $gp->currency ?? 'MYR',
                'duration' => $gp->duration,
                'duration_days' => count($itineraryData) > 0 ? count($itineraryData) : 3,
                'duration_nights' => count($itineraryData) > 1 ? (count($itineraryData) - 1) : 2,
                'star_rating' => $gp->star_rating,
                'vehicle_type' => $gp->vehicle_type,
                'accommodation_type' => $gp->accommodation_type,
                'addon_amenities' => $gp->addon_amenities,
                'description' => $gp->short_description ?? $gp->description,
                'days_count' => count($itineraryData),
                'destination_id' => $gp->destination_id,
                'destination_name' => $gp->destination->name ?? '',
            ];
        });

        return response()->json($results->values());
    }

    /**
     * Get single Group Package details with full itinerary and amenities
     */
    public function getGroupPackage($id)
    {
        $gp = GroupPackage::with(['destination' => function ($q) {
            $q->withTrashed();
        }, 'supplier'])->findOrFail($id);

        $itineraryData = is_array($gp->itinerary) ? $gp->itinerary : (json_decode($gp->itinerary, true) ?: []);
        $amenities = is_array($gp->addon_amenities) ? $gp->addon_amenities : (json_decode($gp->addon_amenities, true) ?: []);

        return response()->json([
            'id' => $gp->id,
            'name' => $gp->name,
            'slug' => $gp->slug,
            'price' => (float)$gp->price,
            'currency' => $gp->currency ?? 'MYR',
            'duration' => $gp->duration,
            'duration_days' => count($itineraryData) > 0 ? count($itineraryData) : 3,
            'duration_nights' => count($itineraryData) > 1 ? (count($itineraryData) - 1) : 2,
            'star_rating' => $gp->star_rating,
            'vehicle_type' => $gp->vehicle_type,
            'accommodation_type' => $gp->accommodation_type,
            'description' => $gp->description,
            'short_description' => $gp->short_description,
            'itinerary' => $itineraryData,
            'addon_amenities' => $amenities,
            'destination_id' => $gp->destination_id,
            'destination' => $gp->destination ? ['id' => $gp->destination->id, 'name' => $gp->destination->name] : null,
        ]);
    }

    /**
     * Get Supplier Assets from Operations master
     */
    public function supplierAssets(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $rawType = trim(strtolower($supplier->type));

        // Normalize type
        $type = $rawType;
        if (str_contains($rawType, 'hotel') || str_contains($rawType, 'accommodation')) {
            $type = 'hotel';
        } elseif (str_contains($rawType, 'transport')) {
            $type = 'transport';
        } elseif (str_contains($rawType, 'activity') || str_contains($rawType, 'attraction')) {
            $type = 'activity';
        } elseif (str_contains($rawType, 'ticket')) {
            $type = 'ticket';
        } elseif (str_contains($rawType, 'meal')) {
            $type = 'meal';
        }

        $data = [
            'supplier' => $supplier,
            'type' => $type,
            'assets' => [],
        ];

        switch ($type) {
            case 'hotel':
                $hotels = Hotel::where('supplier_id', $id)->with('rooms')->where('is_active', true)->get();
                if ($hotels->isNotEmpty()) {
                    $data['assets'] = $hotels->map(function ($hotel) {
                        return [
                            'id' => $hotel->id,
                            'hotel_id' => $hotel->id,
                            'name' => $hotel->name,
                            'star_rating' => $hotel->star_rating ?? 4,
                            'is_core_service' => false,
                            'accommodation_type' => 'Hotel',
                            'price' => $hotel->rooms->first() ? $hotel->rooms->first()->base_price : 0,
                            'currency' => $hotel->currency ?? 'MYR',
                            'description' => $hotel->description,
                            'supplier_id' => $hotel->supplier_id,
                            'rooms' => $hotel->rooms->map(fn($r) => [
                                'id' => $r->id,
                                'hotel_id' => $hotel->id,
                                'hotel_room_id' => $r->id,
                                'room_type' => $r->room_type,
                                'base_price' => (float)$r->base_price,
                                'price' => (float)$r->base_price,
                                'currency' => $r->currency ?? $hotel->currency ?? 'MYR',
                                'capacity' => $r->capacity ?? 2,
                                'is_core_service' => false,
                            ])->values()->all(),
                        ];
                    })->values();
                }
                break;

            case 'transport':
                $transports = Transport::where('supplier_id', $id)->where('is_active', true)->get();
                if ($transports->isNotEmpty()) {
                    $data['assets'] = $transports->map(function ($transport) {
                        return [
                            'id' => $transport->id,
                            'transport_id' => $transport->id,
                            'name' => $transport->name,
                            'vehicle_type' => $transport->vehicle_type ?: $transport->name,
                            'base_price' => (float)$transport->base_price,
                            'price' => (float)$transport->base_price,
                            'currency' => $transport->currency ?? 'MYR',
                            'description' => $transport->destination ? ('Transfer to ' . $transport->destination) : '',
                            'duration' => $transport->duration,
                            'supplier_id' => $transport->supplier_id,
                            'is_core_service' => false,
                            'capacity' => $transport->capacity ?: 'N/A',
                        ];
                    })->values();
                }
                break;

            case 'activity':
                $activities = Activity::where('supplier_id', $id)->where('is_active', true)->get();
                if ($activities->isNotEmpty()) {
                    $data['assets'] = $activities->map(function ($act) {
                        $basePrice = (float)$act->base_price;
                        return [
                            'id' => $act->id,
                            'activity_id' => $act->id,
                            'name' => $act->name,
                            'base_price' => $basePrice,
                            'adult_price' => $basePrice,
                            'child_price' => (float)($basePrice * 0.7),
                            'child_2_6_price' => (float)($basePrice * 0.5),
                            'child_6_11_price' => (float)($basePrice * 0.75),
                            'currency' => 'MYR',
                            'description' => $act->description,
                            'supplier_id' => $act->supplier_id,
                            'is_core_service' => false,
                            'duration' => $act->duration,
                        ];
                    })->values();
                }
                break;

            case 'ticket':
                $tickets = EntryTicket::where('supplier_id', $id)->where('is_active', true)->get();
                if ($tickets->isNotEmpty()) {
                    $data['assets'] = $tickets->map(function ($ticket) {
                        $adultPrice = (float)$ticket->adult_price;
                        return [
                            'id' => $ticket->id,
                            'ticket_id' => $ticket->id,
                            'attraction_name' => $ticket->attraction_name,
                            'name' => $ticket->attraction_name,
                            'adult_price' => $adultPrice,
                            'child_price' => (float)($ticket->child_price ?: ($adultPrice * 0.7)),
                            'currency' => $ticket->currency ?? 'MYR',
                            'description' => 'Entry Ticket: ' . $ticket->attraction_name,
                            'supplier_id' => $ticket->supplier_id,
                            'is_core_service' => false,
                        ];
                    })->values();
                }
                break;

            case 'meal':
                $meals = Meal::where('supplier_id', $id)->where('is_active', true)->get();
                if ($meals->isNotEmpty()) {
                    $data['assets'] = $meals->map(function ($meal) {
                        return [
                            'id' => $meal->id,
                            'meal_id' => $meal->id,
                            'name' => $meal->name,
                            'price' => (float)$meal->price,
                            'currency' => 'MYR',
                            'type' => $meal->type ?: 'Meal',
                            'description' => $meal->description ?? '',
                            'supplier_id' => $meal->supplier_id,
                            'is_core_service' => false,
                        ];
                    })->values();
                }
                break;
        }

        // If no operational assets found for this supplier, check legacy Service table
        if (empty($data['assets'])) {
            $services = Service::where('supplier_id', $id)->where('is_active', true)->get();
            $data['assets'] = $services->map(function ($service) {
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

    /**
     * Get Fixed Itineraries (Master Packages)
     */
    public function fixedItineraries(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $search = $request->query('search');

        $query = \App\Models\FixedItinerary::with(['destination', 'supplier'])->where('status', 'active');

        if ($destinationId) {
            $query->where('destination_id', $destinationId);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $results = $query->orderBy('title')->get()->map(function ($fi) {
            $days = is_array($fi->days) ? $fi->days : (json_decode($fi->days, true) ?: []);
            return [
                'id' => $fi->id,
                'name' => $fi->title,
                'title' => $fi->title,
                'price' => (float)$fi->fixed_price,
                'fixed_price' => (float)$fi->fixed_price,
                'currency' => $fi->currency ?: 'MYR',
                'duration_days' => $fi->duration_days ?: (count($days) ?: 1),
                'duration_nights' => $fi->duration_nights ?: 0,
                'description' => $fi->short_description ?: $fi->itinerary_description,
                'destination_id' => $fi->destination_id,
                'destination_name' => $fi->destination->name ?? '',
                'days_count' => count($days),
                'days' => $days,
            ];
        });

        return response()->json($results->values());
    }

    /**
     * Get single Fixed Itinerary with days and full structure
     */
    public function getFixedItinerary($id)
    {
        $fi = \App\Models\FixedItinerary::with(['destination', 'supplier'])->findOrFail($id);
        $days = is_array($fi->days) ? $fi->days : (json_decode($fi->days, true) ?: []);

        return response()->json([
            'id' => $fi->id,
            'name' => $fi->title,
            'title' => $fi->title,
            'fixed_price' => (float)$fi->fixed_price,
            'currency' => $fi->currency ?: 'MYR',
            'duration_days' => $fi->duration_days ?: (count($days) ?: 1),
            'duration_nights' => $fi->duration_nights ?: 0,
            'description' => $fi->short_description ?: $fi->itinerary_description,
            'highlights' => $fi->highlights ?: [],
            'inclusions' => $fi->inclusions ?: [],
            'exclusions' => $fi->exclusions ?: [],
            'terms_conditions' => $fi->terms_conditions,
            'destination_id' => $fi->destination_id,
            'destination_name' => $fi->destination->name ?? '',
            'days' => $days,
        ]);
    }
}
