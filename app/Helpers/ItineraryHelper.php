<?php

namespace App\Helpers;

use App\Models\Destination;
use App\Models\Service;

class ItineraryHelper
{
    /**
     * Validate itinerary structure
     *
     * @param array $itinerary
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateItinerary($itinerary)
    {
        $errors = [];

        if (!is_array($itinerary)) {
            return ['valid' => false, 'errors' => ['Itinerary must be an array']];
        }

        foreach ($itinerary as $index => $day) {
            $dayNum = $index + 1;

            // Validate required fields
            if (!isset($day['day'])) {
                $errors[] = "Day {$dayNum}: 'day' field is required";
            }

            if (!isset($day['title'])) {
                $errors[] = "Day {$dayNum}: 'title' field is required";
            }

            // Validate places structure
            if (isset($day['places']) && !is_array($day['places'])) {
                $errors[] = "Day {$dayNum}: 'places' must be an array";
            }

            // Validate activities structure
            if (isset($day['activities']) && !is_array($day['activities'])) {
                $errors[] = "Day {$dayNum}: 'activities' must be an array";
            }

            // Validate transport structure
            if (isset($day['transport']) && !is_array($day['transport'])) {
                $errors[] = "Day {$dayNum}: 'transport' must be an array";
            }

            // Validate hotel structure
            if (isset($day['hotel']) && !is_array($day['hotel'])) {
                $errors[] = "Day {$dayNum}: 'hotel' must be an array";
            }

            // Validate spots structure
            if (isset($day['spots']) && !is_array($day['spots'])) {
                $errors[] = "Day {$dayNum}: 'spots' must be an array";
            }

            // Validate tickets structure
            if (isset($day['tickets']) && !is_array($day['tickets'])) {
                $errors[] = "Day {$dayNum}: 'tickets' must be an array";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Calculate total cost from itinerary
     *
     * @param array $itinerary
     * @param string $currency
     * @return array
     */
    public static function calculateTotalCost($itinerary, $currency = 'MYR')
    {
        $costs = [
            'hotels' => 0,
            'transport' => 0,
            'activities' => 0,
            'entry_tickets' => 0,
            'total' => 0,
            'currency' => $currency
        ];

        if (!is_array($itinerary)) {
            return $costs;
        }

        foreach ($itinerary as $day) {
            // 1. Calculate hotel costs: (price_per_night + add_on_price) * quantity
            if (isset($day['hotels']) && is_array($day['hotels'])) {
                foreach ($day['hotels'] as $hotel) {
                    $price = (float) ($hotel['price_per_night'] ?? 0);
                    $addon = (float) ($hotel['add_on_price'] ?? 0);
                    $qty = (float) ($hotel['quantity'] ?? 1);
                    $costs['hotels'] += ($price + $addon) * $qty;
                }
            } elseif (isset($day['hotel']['price_per_night'])) {
                $price = (float) $day['hotel']['price_per_night'];
                $addon = (float) ($day['hotel']['add_on_price'] ?? 0);
                $qty = (float) ($day['hotel']['quantity'] ?? 1);
                $costs['hotels'] += ($price + $addon) * $qty;
            }

            // 2. Calculate transport costs: price
            if (isset($day['transport']) && is_array($day['transport'])) {
                foreach ($day['transport'] as $transport) {
                    $costs['transport'] += (float) ($transport['price'] ?? 0);
                }
            }

            // 3. Calculate activity/spot costs: hours * rate OR per-adult/child rates
            if (isset($day['activities']) && is_array($day['activities'])) {
                foreach ($day['activities'] as $act) {
                    // Check for entry ticket per pax: (adult * qty) + (child * qty)
                    if (isset($act['entry_ticket'])) {
                        $et = $act['entry_ticket'];
                        $adult_price = (float) ($et['adult_price'] ?? $et['price'] ?? 0);
                        $child_2_6_price = (float) ($et['child_2_6_price'] ?? 0);
                        $child_6_11_price = (float) ($et['child_6_11_price'] ?? 0);

                        $adult_qty = (float) ($et['adult_qty'] ?? 0);
                        $child_2_6_qty = (float) ($et['child_2_6_qty'] ?? 0);
                        $child_6_11_qty = (float) ($et['child_6_11_qty'] ?? 0);

                        $actCost = ($adult_price * $adult_qty) + ($child_2_6_price * $child_2_6_qty) + ($child_6_11_price * $child_6_11_qty);

                        // If no quantities provided, fallback to old behavior (take single price)
                        if (($adult_qty + $child_2_6_qty + $child_6_11_qty) == 0) {
                            $actCost = (float) ($et['price'] ?? 0);
                        }

                        $costs['activities'] += $actCost;
                    }

                    // Specific "Hours" calculation for spots/activities
                    if (isset($act['hours']) && isset($act['price_per_hour'])) {
                        $costs['activities'] += ((float) $act['hours'] * (float) $act['price_per_hour']);
                    }
                }
            }

            // 4. Calculate entry ticket costs from places (same pax-based logic)
            if (isset($day['places']) && is_array($day['places'])) {
                foreach ($day['places'] as $place) {
                    if (isset($place['entry_ticket'])) {
                        $et = $place['entry_ticket'];
                        $adult_price = (float) ($et['adult_price'] ?? $et['price'] ?? 0);
                        $child_2_6_price = (float) ($et['child_2_6_price'] ?? 0);
                        $child_6_11_price = (float) ($et['child_6_11_price'] ?? 0);

                        $adult_qty = (float) ($et['adult_qty'] ?? 0);
                        $child_2_6_qty = (float) ($et['child_2_6_qty'] ?? 0);
                        $child_6_11_qty = (float) ($et['child_6_11_qty'] ?? 0);

                        $ticketCost = ($adult_price * $adult_qty) + ($child_2_6_price * $child_2_6_qty) + ($child_6_11_price * $child_6_11_qty);

                        if (($adult_qty + $child_2_6_qty + $child_6_11_qty) == 0) {
                            $ticketCost = (float) ($et['price'] ?? 0);
                        }

                        $costs['entry_tickets'] += $ticketCost;
                    }

                    if (isset($place['hours']) && isset($place['price_per_hour'])) {
                        $costs['entry_tickets'] += ((float) $place['hours'] * (float) $place['price_per_hour']);
                    }
                }
            }

            // 5. Calculate Tourist Spot costs: hours * price_per_hour
            if (isset($day['spots']) && is_array($day['spots'])) {
                foreach ($day['spots'] as $spot) {
                    $costs['entry_tickets'] += ((float) ($spot['hours'] ?? 0) * (float) ($spot['price_per_hour'] ?? 0));
                }
            }

            // 5b. Calculate direct Entry Tickets costs
            if (isset($day['tickets']) && is_array($day['tickets'])) {
                foreach ($day['tickets'] as $ticket) {
                    $tPrice = (float) ($ticket['price'] ?? $ticket['adult_price'] ?? 0);
                    $tQty = (float) ($ticket['quantity'] ?? 1);
                    $costs['entry_tickets'] += ($tPrice * $tQty);
                }
            }

            // 6. Calculate meal costs: price * quantity
            if (isset($day['meals']) && is_array($day['meals'])) {
                foreach ($day['meals'] as $meal) {
                    $mPrice = (float) ($meal['price'] ?? 0);
                    $mQty = (float) ($meal['quantity'] ?? 1);
                    $costs['activities'] += ($mPrice * $mQty);
                }
            }
        }

        $costs['total'] = $costs['hotels'] + $costs['transport'] + $costs['activities'] + $costs['entry_tickets'];

        return $costs;
    }

    /**
     * Generate a sample itinerary structure
     *
     * @param int $days
     * @param int $placeId
     * @return array
     */
    public static function generateSampleItinerary($days = 3, $destinationId = null)
    {
        $itinerary = [];
        $destination = $destinationId ? \App\Models\Country::find($destinationId) : null;
        $destinationName = $destination ? $destination->name : 'Malaysia';

        // Query real operational inventory
        $hotel = \App\Models\Hotel::with('rooms')
            ->where('is_active', true)
            ->where(function ($q) use ($destinationId, $destinationName) {
                if ($destinationId) {
                    $q->where('destination_id', $destinationId);
                }
                if ($destinationName) {
                    $q->orWhereHas('destination', function ($dq) use ($destinationName) {
                        $dq->withTrashed()->where('country', $destinationName)->orWhere('name', $destinationName);
                    });
                }
            })->first() ?: \App\Models\Hotel::with('rooms')->where('is_active', true)->first();

        $hotelRoom = $hotel && $hotel->rooms->isNotEmpty() ? $hotel->rooms->first() : null;

        $spots = \App\Models\TouristSpot::where('is_active', true)
            ->where(function ($q) use ($destinationId, $destinationName) {
                if ($destinationId) {
                    $q->where('country_id', $destinationId);
                }
                if ($destinationName) {
                    $q->orWhereHas('destination', function ($dq) use ($destinationName) {
                        $dq->withTrashed()->where('country', $destinationName)->orWhere('name', $destinationName);
                    });
                }
            })->get();

        $transports = \App\Models\Transport::where('is_active', true)->get();
        $tickets = \App\Models\EntryTicket::where('is_active', true)->get();
        $meals = \App\Models\Meal::where('is_active', true)->get();

        for ($i = 1; $i <= $days; $i++) {
            $spotIndex = ($i - 1) % ($spots->count() ?: 1);
            $currentSpot = $spots->isNotEmpty() ? $spots->get($spotIndex) : null;

            $ticketIndex = ($i - 1) % ($tickets->count() ?: 1);
            $currentTicket = $tickets->isNotEmpty() ? $tickets->get($ticketIndex) : null;

            $transportIndex = ($i - 1) % ($transports->count() ?: 1);
            $currentTransport = $transports->isNotEmpty() ? $transports->get($transportIndex) : null;

            $dayData = [
                'day' => $i,
                'title' => self::getDayTitle($i, $days),
                'places' => [],
                'spots' => [],
                'hotel' => [
                    'name' => $hotel ? $hotel->name : ('Hotel ' . $destinationName),
                    'hotel_id' => $hotel ? $hotel->id : null,
                    'hotel_room_id' => $hotelRoom ? $hotelRoom->id : null,
                    'type' => $hotelRoom ? $hotelRoom->room_type : 'Standard',
                    'star_rating' => $hotel ? ($hotel->star_rating ?? 4) : 4,
                    'check_in' => '14:00',
                    'check_out' => '12:00',
                    'price_per_night' => $hotelRoom ? (float)$hotelRoom->base_price : 120.00,
                    'currency' => $hotel ? ($hotel->currency ?? 'MYR') : 'MYR',
                    'amenities' => ['WiFi', 'Breakfast', 'Air Conditioning']
                ],
                'hotels' => $hotel ? [[
                    'name' => $hotel->name,
                    'hotel_id' => $hotel->id,
                    'hotel_room_id' => $hotelRoom ? $hotelRoom->id : null,
                    'star_rating' => $hotel->star_rating ?? 4,
                    'type' => $hotelRoom ? $hotelRoom->room_type : 'Standard',
                    'price_per_night' => $hotelRoom ? (float)$hotelRoom->base_price : 120.00,
                    'currency' => $hotel->currency ?? 'MYR',
                    'quantity' => 1,
                    'add_on_price' => 0,
                    'service_id' => null,
                    'supplier_id' => $hotel->supplier_id
                ]] : [],
                'transport' => [
                    [
                        'name' => $currentTransport ? $currentTransport->name : ($i === 1 ? 'Airport Transfer' : 'City Transfer'),
                        'transport_id' => $currentTransport ? $currentTransport->id : null,
                        'type' => $i === 1 ? 'Airport Transfer' : 'Local Transport',
                        'mode' => $currentTransport ? ($currentTransport->vehicle_type ?: $currentTransport->name) : 'Private Car',
                        'vehicle_type' => $currentTransport ? ($currentTransport->vehicle_type ?: $currentTransport->name) : 'Private Car',
                        'from' => $i === 1 ? 'Airport' : 'Hotel',
                        'to' => $i === 1 ? 'Hotel' : ($currentSpot ? $currentSpot->name : 'Attraction'),
                        'price' => $currentTransport ? (float)$currentTransport->base_price : ($i === 1 ? 120.00 : 35.00),
                        'currency' => $currentTransport ? ($currentTransport->currency ?? 'MYR') : 'MYR',
                        'duration' => $currentTransport ? ($currentTransport->duration ?: '45 minutes') : '45 minutes',
                        'service_id' => null,
                        'supplier_id' => $currentTransport ? $currentTransport->supplier_id : null
                    ]
                ],
                'activities' => [],
                'meals' => [
                    'breakfast' => 'Included at hotel',
                    'lunch' => 'Local restaurant',
                    'dinner' => $i === $days ? 'Farewell dinner' : 'Included'
                ],
                'notes' => 'Comfortable walking shoes recommended. Tour program operated by Tourliz.'
            ];

            // Add spot if available
            if ($currentSpot) {
                $dayData['spots'][] = [
                    'name' => $currentSpot->name,
                    'spot_id' => $currentSpot->id,
                    'tourist_spot_id' => $currentSpot->id,
                    'description' => $currentSpot->description ?? '',
                    'hours' => 2,
                    'price_per_hour' => 0,
                    'service_id' => null,
                    'supplier_id' => $currentSpot->supplier_id
                ];
            }

            // Add entry ticket if available
            if ($currentTicket) {
                $dayData['places'][] = [
                    'attraction_name' => $currentTicket->attraction_name,
                    'ticket_id' => $currentTicket->id,
                    'entry_ticket_id' => $currentTicket->id,
                    'destination_id' => $currentTicket->destination_id,
                    'description' => 'Entry Ticket: ' . $currentTicket->attraction_name,
                    'entry_ticket' => [
                        'required' => true,
                        'adult_price' => (float)$currentTicket->adult_price,
                        'child_price' => (float)($currentTicket->child_price ?: ($currentTicket->adult_price * 0.7)),
                        'adult_qty' => 2,
                        'child_2_6_qty' => 0,
                        'child_6_11_qty' => 0,
                        'currency' => $currentTicket->currency ?? 'MYR',
                        'booking_required' => false
                    ],
                    'service_id' => null,
                    'supplier_id' => $currentTicket->supplier_id
                ];
            }

            // Last day modifications
            if ($i === $days && $transports->count() > 1) {
                $dayData['title'] = 'Departure';
                $returnTransport = $transports->last();
                $dayData['transport'][] = [
                    'name' => $returnTransport->name,
                    'transport_id' => $returnTransport->id,
                    'type' => 'Airport Drop-off',
                    'mode' => $returnTransport->vehicle_type ?: $returnTransport->name,
                    'vehicle_type' => $returnTransport->vehicle_type ?: $returnTransport->name,
                    'from' => 'Hotel',
                    'to' => 'Airport',
                    'price' => (float)$returnTransport->base_price,
                    'currency' => $returnTransport->currency ?? 'MYR',
                    'duration' => $returnTransport->duration ?: '45 minutes',
                    'service_id' => null,
                    'supplier_id' => $returnTransport->supplier_id
                ];
            }

            $itinerary[] = $dayData;
        }

        return $itinerary;
    }

    /**
     * Get appropriate title for day based on position
     *
     * @param int $day
     * @param int $totalDays
     * @return string
     */
    private static function getDayTitle($day, $totalDays)
    {
        if ($day === 1) {
            return 'Arrival and Welcome';
        } elseif ($day === $totalDays) {
            return 'Departure';
        } else {
            return 'Day ' . $day . ' - Exploration';
        }
    }

    /**
     * Enrich itinerary with database data
     *
     * @param array $itinerary
     * @return array
     */
    public static function enrichItinerary($itinerary)
    {
        if (!is_array($itinerary)) {
            return $itinerary;
        }

        foreach ($itinerary as &$day) {
            // Enrich hotel with service or operational data
            if (isset($day['hotel']) && is_array($day['hotel'])) {
                if (!empty($day['hotel']['hotel_id'])) {
                    $hModel = \App\Models\Hotel::find($day['hotel']['hotel_id']);
                    if ($hModel) {
                        $day['hotel']['name'] = $hModel->name;
                        $day['hotel']['star_rating'] = $hModel->star_rating ?? 4;
                        $day['hotel']['currency'] = $hModel->currency ?? 'MYR';
                    }
                    if (!empty($day['hotel']['hotel_room_id'])) {
                        $rModel = \App\Models\HotelRoom::find($day['hotel']['hotel_room_id']);
                        if ($rModel) {
                            $day['hotel']['type'] = $rModel->room_type;
                            $day['hotel']['price_per_night'] = (float)$rModel->base_price;
                        }
                    }
                } elseif (isset($day['hotel']['service_id'])) {
                    $service = Service::find($day['hotel']['service_id']);
                    if ($service) {
                        $day['hotel']['name'] = $service->name;
                        $day['hotel']['type'] = 'Core Service';
                        $day['hotel']['price_per_night'] = $service->price;
                        $day['hotel']['currency'] = $service->currency ?? 'MYR';
                        $day['hotel']['image'] = $service->image ?? null;
                        $day['hotel']['description'] = $service->short_description ?? $service->description ?? null;
                    }
                }
            }

            // Enrich hotels inside hotels array with service or operational data
            if (isset($day['hotels']) && is_array($day['hotels'])) {
                foreach ($day['hotels'] as &$hotel) {
                    if (is_array($hotel)) {
                        if (!empty($hotel['hotel_id'])) {
                            $hModel = \App\Models\Hotel::find($hotel['hotel_id']);
                            if ($hModel) {
                                $hotel['name'] = $hModel->name;
                                $hotel['star_rating'] = $hModel->star_rating ?? 4;
                                $hotel['currency'] = $hModel->currency ?? 'MYR';
                            }
                            if (!empty($hotel['hotel_room_id'])) {
                                $rModel = \App\Models\HotelRoom::find($hotel['hotel_room_id']);
                                if ($rModel) {
                                    $hotel['type'] = $rModel->room_type;
                                    $hotel['price_per_night'] = (float)$rModel->base_price;
                                }
                            }
                        } elseif (isset($hotel['service_id'])) {
                            $service = Service::find($hotel['service_id']);
                            if ($service) {
                                $hotel['name'] = $service->name;
                                $hotel['type'] = $service->accommodation_type ?: 'Core Service';
                                $hotel['price_per_night'] = $service->price;
                                $hotel['currency'] = $service->currency ?? 'MYR';
                                $hotel['image'] = $service->image ?? null;
                                $hotel['description'] = $service->short_description ?? $service->description ?? null;
                            }
                        }
                    }
                }
            }

            // Enrich transport with service or operational data
            if (isset($day['transport']) && is_array($day['transport'])) {
                foreach ($day['transport'] as &$trans) {
                    if (is_array($trans)) {
                        if (!empty($trans['transport_id'])) {
                            $tModel = \App\Models\Transport::find($trans['transport_id']);
                            if ($tModel) {
                                $trans['name'] = $tModel->name;
                                $trans['mode'] = $tModel->vehicle_type ?: $tModel->name;
                                $trans['vehicle_type'] = $tModel->vehicle_type ?: $tModel->name;
                                $trans['price'] = (float)$tModel->base_price;
                                $trans['currency'] = $tModel->currency ?? 'MYR';
                            }
                        } elseif (isset($trans['service_id'])) {
                            $service = Service::find($trans['service_id']);
                            if ($service) {
                                $trans['name'] = $service->name;
                                $trans['mode'] = $service->name;
                                $trans['type'] = $service->vehicle_type ?: $service->name;
                                $trans['price'] = $service->price;
                                $trans['currency'] = $service->currency ?? 'MYR';
                                $trans['image'] = $service->image ?? null;
                                $trans['description'] = $service->short_description ?? $service->description ?? null;
                            }
                        }
                    }
                }
            }

            // Enrich places with database data
            if (isset($day['places']) && is_array($day['places'])) {
                foreach ($day['places'] as &$place) {
                    if (is_string($place)) {
                        $place = ['name' => $place];
                    }
                    if (is_array($place)) {
                        if (!empty($place['spot_id']) || !empty($place['tourist_spot_id'])) {
                            $sId = $place['spot_id'] ?? $place['tourist_spot_id'];
                            $tsModel = \App\Models\TouristSpot::with('supplier', 'destination')->find($sId);
                            if ($tsModel) {
                                $place['name'] = $tsModel->name;
                                $place['supplier_name'] = $tsModel->supplier ? $tsModel->supplier->name : ($place['supplier_name'] ?? null);
                                $place['destination'] = $tsModel->destination ? $tsModel->destination->name : ($place['destination'] ?? null);
                            }
                        }
                        if (!empty($place['ticket_id']) || !empty($place['entry_ticket_id'])) {
                            $tId = $place['ticket_id'] ?? $place['entry_ticket_id'];
                            $etModel = \App\Models\EntryTicket::find($tId);
                            if ($etModel) {
                                $place['name'] = $etModel->attraction_name;
                                $place['attraction_name'] = $etModel->attraction_name;
                                $place['currency'] = $etModel->currency ?? 'MYR';
                                if (isset($place['entry_ticket']) && is_array($place['entry_ticket'])) {
                                    $place['entry_ticket']['adult_price'] = (float)$etModel->adult_price;
                                    $place['entry_ticket']['child_price'] = (float)($etModel->child_price ?: ($etModel->adult_price * 0.7));
                                    $place['entry_ticket']['currency'] = $etModel->currency ?? 'MYR';
                                }
                            }
                        }
                        if (isset($place['destination_id'])) {
                            $destinationModel = Destination::find($place['destination_id']);
                            if ($destinationModel) {
                                $place['place_name'] = $destinationModel->name;
                                $place['place_slug'] = $destinationModel->slug;
                                $place['place_image'] = $destinationModel->image ?? null;
                            }
                        }
                        if (isset($place['service_id'])) {
                            $service = Service::find($place['service_id']);
                            if ($service) {
                                $place['name'] = $service->name;
                                $place['attraction_name'] = $service->name;
                                $place['price'] = $service->price;
                                $place['currency'] = $service->currency ?? 'MYR';
                                $place['image'] = $service->image ?? null;
                                $place['description'] = $service->short_description ?? $service->description ?? null;
                                if (isset($place['entry_ticket']) && is_array($place['entry_ticket'])) {
                                    $place['entry_ticket']['adult_price'] = $service->price;
                                    $place['entry_ticket']['child_2_6_price'] = $service->price_2_6 ?: ($service->price * 0.5);
                                    $place['entry_ticket']['child_6_11_price'] = $service->price_6_10 ?: ($service->price * 0.75);
                                    $place['entry_ticket']['currency'] = $service->currency ?? 'MYR';
                                }
                            }
                        }
                    }
                }
            }

            // Enrich activities with service or operational data
            if (isset($day['activities']) && is_array($day['activities'])) {
                foreach ($day['activities'] as &$activity) {
                    if (is_string($activity)) {
                        $activity = ['name' => $activity];
                    }
                    if (is_array($activity)) {
                        if (!empty($activity['activity_id'])) {
                            $actModel = \App\Models\Activity::find($activity['activity_id']);
                            if ($actModel) {
                                $activity['name'] = $actModel->name;
                                $activity['description'] = $actModel->description;
                                $activity['duration'] = $actModel->duration;
                                if (isset($activity['entry_ticket']) && is_array($activity['entry_ticket'])) {
                                    $activity['entry_ticket']['adult_price'] = (float)$actModel->base_price;
                                    $activity['entry_ticket']['currency'] = 'MYR';
                                }
                            }
                        } elseif (isset($activity['service_id'])) {
                            $service = Service::find($activity['service_id']);
                            if ($service) {
                                $activity['name'] = $service->name;
                                $activity['price'] = $service->price;
                                $activity['currency'] = $service->currency ?? 'MYR';
                                $activity['image'] = $service->image ?? null;
                                $activity['description'] = $service->short_description ?? $service->description ?? null;
                                if (isset($activity['entry_ticket']) && is_array($activity['entry_ticket'])) {
                                    $activity['entry_ticket']['adult_price'] = $service->price;
                                    $activity['entry_ticket']['child_2_6_price'] = $service->price_2_6 ?: ($service->price * 0.5);
                                    $activity['entry_ticket']['child_6_11_price'] = $service->price_6_10 ?: ($service->price * 0.75);
                                    $activity['entry_ticket']['currency'] = $service->currency ?? 'MYR';
                                }
                            }
                        }
                    }
                }
            }

            // Enrich spots with service or operational data
            if (isset($day['spots']) && is_array($day['spots'])) {
                foreach ($day['spots'] as &$spot) {
                    if (is_string($spot)) {
                        $spot = ['name' => $spot];
                    }
                    if (is_array($spot)) {
                        if (!empty($spot['spot_id']) || !empty($spot['tourist_spot_id'])) {
                            $sId = $spot['spot_id'] ?? $spot['tourist_spot_id'];
                            $tsModel = \App\Models\TouristSpot::with('supplier', 'destination')->find($sId);
                            if ($tsModel) {
                                $spot['name'] = $tsModel->name;
                                $spot['description'] = $tsModel->description ?? '';
                                $spot['image'] = $tsModel->image_url ?? null;
                                $spot['supplier_name'] = $tsModel->supplier ? $tsModel->supplier->name : ($spot['supplier_name'] ?? null);
                                $spot['destination'] = $tsModel->destination ? $tsModel->destination->name : ($spot['destination'] ?? null);
                            }
                        } elseif (isset($spot['service_id'])) {
                            $service = Service::find($spot['service_id']);
                            if ($service) {
                                $spot['name'] = $service->name;
                                $spot['description'] = $service->short_description ?? $service->description ?? null;
                                $spot['price_per_hour'] = $service->price;
                                $spot['hours'] = $spot['hours'] ?? 2;
                                $spot['image'] = $service->image ?? null;
                            }
                        }
                    }
                }
            }

            // Enrich meals_list with service data
            if (isset($day['meals_list']) && is_array($day['meals_list'])) {
                foreach ($day['meals_list'] as &$mealItem) {
                    if (is_array($mealItem) && isset($mealItem['service_id'])) {
                        $service = Service::find($mealItem['service_id']);
                        if ($service) {
                            $mealItem['name'] = $service->name;
                            $mealItem['price'] = $service->price;
                            $mealItem['type'] = $service->accommodation_type ?: $service->vehicle_type ?: 'Meal';
                            $mealItem['description'] = $service->short_description ?? $service->description ?? null;
                        }
                    }
                }
            }

            // Enrich meals (B2B/B2C meals structure) with service data
            if (isset($day['meals']) && is_array($day['meals'])) {
                foreach ($day['meals'] as &$meal) {
                    if (is_array($meal) && isset($meal['service_id'])) {
                        $service = Service::find($meal['service_id']);
                        if ($service) {
                            $meal['name'] = '[' . ($service->accommodation_type ?: $service->vehicle_type ?: 'Meal') . '] ' . $service->name;
                            $meal['price'] = $service->price;
                            $meal['currency'] = $service->currency ?? 'MYR';
                        }
                    }
                }
            }

            // Enrich direct tickets with operational entry ticket data
            if (isset($day['tickets']) && is_array($day['tickets'])) {
                foreach ($day['tickets'] as &$ticket) {
                    if (is_string($ticket)) {
                        $ticket = ['name' => $ticket, 'price' => 0];
                    }
                    if (is_array($ticket)) {
                        if (!empty($ticket['ticket_id']) || !empty($ticket['entry_ticket_id'])) {
                            $tId = $ticket['ticket_id'] ?? $ticket['entry_ticket_id'];
                            $etModel = \App\Models\EntryTicket::with('supplier')->find($tId);
                            if ($etModel) {
                                $ticket['name'] = $etModel->attraction_name;
                                $ticket['price'] = (float)$etModel->adult_price;
                                $ticket['supplier_name'] = $etModel->supplier ? $etModel->supplier->name : ($ticket['supplier_name'] ?? '');
                                $ticket['currency'] = $etModel->currency ?? 'MYR';
                            }
                        }
                    }
                }
            }
        }

        return $itinerary;
    }

    /**
     * Format itinerary for API response
     *
     * @param array $itinerary
     * @param bool $includeCosts
     * @return array
     */
    public static function formatForApi($itinerary, $includeCosts = true)
    {
        $enriched = self::enrichItinerary($itinerary);

        $response = [
            'days' => count($enriched),
            'itinerary' => $enriched
        ];

        if ($includeCosts) {
            $response['cost_breakdown'] = self::calculateTotalCost($enriched);
        }

        return $response;
    }
}
