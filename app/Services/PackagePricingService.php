<?php

namespace App\Services;

use App\Models\Package;

class PackagePricingService
{
    /**
     * Calculate the price per person for a package based on group size.
     * 
     * @param Package $package
     * @param int $paxCount
     * @return array
     */
    public function calculatePrice(Package $package, int $paxCount, $roomId = null)
    {
        if ($paxCount <= 0) return [
            'total_net' => 0,
            'total_selling' => 0,
            'per_pax' => 0,
            'pax_count' => $paxCount
        ];

        $amenities = $package->addon_amenities ?: [];
        $totalNet = 0;
        $markupPct = $package->markup_percentage ?: 0;

        // Fetch room if roomId provided
        $selectedRoom = null;
        if ($roomId) {
            $selectedRoom = \App\Models\HotelRoom::find($roomId);
        }

        foreach ($amenities as $amenity) {
            $logic = $amenity['logic'] ?? 'fixed';
            $days = (float) ($amenity['days'] ?? 1);
            $qty = (float) ($amenity['qty'] ?? 1);
            $type = $amenity['type'] ?? '';

            if ($logic === 'fixed') {
                $price = (float) ($amenity['price'] ?? 0);
                $totalNet += ($price * $days * $qty);
            } elseif ($logic === 'per_pax') {
                $adPrice = (float) ($amenity['adult_price'] ?? 0);
                $totalNet += ($adPrice * $paxCount);
            } elseif ($logic === 'sharing') {
                $doublePrice = (float) ($amenity['double_price'] ?? 0);
                $singlePrice = (float) ($amenity['single_price'] ?? 0);
                $triplePrice = (float) ($amenity['triple_price'] ?? 0);
                $quadPrice = (float) ($amenity['quad_price'] ?? $amenity['extrabed_price'] ?? 0);

                // Override with specific room price if it belongs to this hotel
                if ($selectedRoom && $type === 'hotel' && $selectedRoom->hotel_id == ($amenity['supplier_id'] ?? null)) {
                    // We assume the room price is the price for the whole room
                    // For packages, we usually divide by 2 if it's sharing, or take full if single.
                    // But if it's a specific room selected by customer, we treat it as "Room Total" 
                    // and divide later by pax for 'per pax' display.
                    $doublePrice = $selectedRoom->base_price; 
                    // If we have a specific room, we might not have single/triple supplement logic easily.
                    // For now, let's just use the room price as the 'base' for the sharing logic.
                }

                if ($paxCount == 3 && $triplePrice > 0) {
                    $totalNet += ($triplePrice * $days);
                } elseif ($paxCount == 4 && $quadPrice > 0) {
                    $totalNet += ($quadPrice * $days);
                } elseif ($paxCount % 2 === 0) {
                    $totalNet += ($doublePrice * ($paxCount / 2) * $days);
                } else {
                    $pairs = floor($paxCount / 2);
                    $totalNet += ($doublePrice * $pairs * $days) + ($singlePrice * $days);
                }
            } elseif ($logic === 'tiered_transport') {
                // Auto-fit vehicles based on capacity
                $vehicles = $amenity['tiered_vehicles'] ?? [];
                if (!empty($vehicles)) {
                    $totalNet += $this->calculateCheapestTransport($paxCount, $vehicles) * $days;
                }
            }
        }

        $sellingTotal = $totalNet + ($totalNet * $markupPct / 100);
        $perPax = $sellingTotal / $paxCount;

        return [
            'total_net' => $totalNet,
            'total_selling' => $sellingTotal,
            'per_pax' => round($perPax, 2),
            'pax_count' => $paxCount
        ];
    }

    /**
     * Cheapest Transport Fit Algorithm
     */
    private function calculateCheapestTransport($pax, $vehicles, &$memo = [])
    {
        if ($pax <= 0) return 0;

        $memoKey = $pax . '-' . implode(',', array_column($vehicles, 'price'));
        if (isset($memo[$memoKey])) return $memo[$memoKey];

        $minCost = PHP_FLOAT_MAX;

        foreach ($vehicles as $v) {
            $capacity = (int) ($v['capacity'] ?? 1);
            $price = (float) ($v['price'] ?? 0);
            
            $cost = $price + $this->calculateCheapestTransport($pax - $capacity, $vehicles, $memo);
            if ($cost < $minCost) {
                $minCost = $cost;
            }
        }

        $memo[$memoKey] = ($minCost === PHP_FLOAT_MAX ? 0 : $minCost);
        return $memo[$memoKey];
    }
}
