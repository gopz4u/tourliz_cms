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
        $destinationName = $destination ? $destination->name : 'Country';

        for ($i = 1; $i <= $days; $i++) {
            $dayData = [
                'day' => $i,
                'title' => self::getDayTitle($i, $days),
                'places' => [
                    [
                        'destination_id' => $destinationId,
                        'name' => $destinationName . ' - Attraction ' . $i,
                        'visit_duration' => '2-3 hours',
                        'entry_ticket' => [
                            'required' => true,
                            'price' => 20 + ($i * 5),
                            'currency' => 'USD',
                            'booking_required' => false
                        ]
                    ]
                ],
                'hotel' => [
                    'name' => 'Hotel ' . $destinationName,
                    'type' => '4-star',
                    'check_in' => '14:00',
                    'check_out' => '12:00',
                    'price_per_night' => 100 + ($i * 10),
                    'currency' => 'USD',
                    'amenities' => ['WiFi', 'Breakfast', 'Pool', 'Gym']
                ],
                'transport' => [
                    [
                        'type' => $i === 1 ? 'Airport Transfer' : 'Local Transport',
                        'mode' => 'Private Car',
                        'from' => $i === 1 ? 'Airport' : 'Hotel',
                        'to' => $i === 1 ? 'Hotel' : 'Attraction',
                        'price' => $i === 1 ? 50 : 30,
                        'currency' => 'USD',
                        'duration' => $i === 1 ? '45 minutes' : '20 minutes'
                    ]
                ],
                'activities' => [
                    [
                        'name' => 'Guided Tour - Day ' . $i,
                        'time' => '10:00 AM',
                        'duration' => '3 hours',
                        'entry_ticket' => [
                            'price' => 35 + ($i * 5),
                            'currency' => 'USD',
                            'booking_required' => true
                        ]
                    ]
                ],
                'meals' => [
                    'breakfast' => 'Included at hotel',
                    'lunch' => 'Local restaurant (own expense)',
                    'dinner' => $i === $days ? 'Farewell dinner included' : 'Not included'
                ],
                'notes' => 'Comfortable walking shoes recommended. Bring sunscreen and water.'
            ];

            // Last day modifications
            if ($i === $days) {
                $dayData['title'] = 'Departure';
                $dayData['transport'][] = [
                    'type' => 'Airport Drop-off',
                    'mode' => 'Private Car',
                    'from' => 'Hotel',
                    'to' => 'Airport',
                    'price' => 50,
                    'currency' => 'USD',
                    'duration' => '45 minutes'
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
            // Enrich hotel with service data if service_id is set
            if (isset($day['hotel']) && is_array($day['hotel']) && isset($day['hotel']['service_id'])) {
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

            // Enrich hotels inside hotels array with service data if service_id is set
            if (isset($day['hotels']) && is_array($day['hotels'])) {
                foreach ($day['hotels'] as &$hotel) {
                    if (is_array($hotel) && isset($hotel['service_id'])) {
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

            // Enrich transport with service data if service_id is set
            if (isset($day['transport']) && is_array($day['transport'])) {
                foreach ($day['transport'] as &$trans) {
                    if (is_array($trans) && isset($trans['service_id'])) {
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

            // Enrich places with database data
            if (isset($day['places']) && is_array($day['places'])) {
                foreach ($day['places'] as &$place) {
                    if (is_array($place)) {
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

            // Enrich activities with service data
            if (isset($day['activities']) && is_array($day['activities'])) {
                foreach ($day['activities'] as &$activity) {
                    if (is_array($activity)) {
                        if (isset($activity['service_id'])) {
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

            // Enrich spots with service data
            if (isset($day['spots']) && is_array($day['spots'])) {
                foreach ($day['spots'] as &$spot) {
                    if (is_array($spot)) {
                        if (isset($spot['service_id'])) {
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
                            $meal['description'] = $service->short_description ?? $service->description ?? null;
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
