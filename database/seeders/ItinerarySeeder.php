<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Place;
use App\Helpers\ItineraryHelper;

class ItinerarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all packages
        $packages = Package::all();

        if ($packages->isEmpty()) {
            $this->command->info('No packages found. Please run PackageSeeder first.');
            return;
        }

        foreach ($packages as $package) {
            // Skip if package already has itinerary
            if ($package->hasItinerary()) {
                $this->command->info("Package '{$package->name}' already has an itinerary. Skipping...");
                continue;
            }

            // Determine number of days
            $days = 3; // default
            if ($package->duration) {
                if (preg_match('/(\d+)\s*days?/i', $package->duration, $matches)) {
                    $days = (int) $matches[1];
                }
            }

            // Generate detailed itinerary
            $itinerary = $this->generateDetailedItinerary($days, $package);

            // Update package
            $package->itinerary = $itinerary;
            $package->save();

            $this->command->info("Generated {$days}-day itinerary for package: {$package->name}");
        }

        $this->command->info('Itinerary seeding completed!');
    }

    /**
     * Generate a detailed itinerary for a package
     *
     * @param int $days
     * @param Package $package
     * @return array
     */
    private function generateDetailedItinerary($days, $package)
    {
        $itinerary = [];
        $place = $package->place;
        $placeName = $place ? $place->name : 'Destination';

        // Sample hotels
        $hotels = [
            ['name' => 'Grand ' . $placeName . ' Hotel', 'type' => '5-star', 'price' => 200],
            ['name' => $placeName . ' Plaza', 'type' => '4-star', 'price' => 150],
            ['name' => 'Comfort Inn ' . $placeName, 'type' => '3-star', 'price' => 100],
        ];

        // Sample activities
        $activities = [
            ['name' => 'City Walking Tour', 'price' => 25, 'duration' => '3 hours'],
            ['name' => 'Museum Visit', 'price' => 15, 'duration' => '2 hours'],
            ['name' => 'Cultural Show', 'price' => 40, 'duration' => '2 hours'],
            ['name' => 'Food Tour', 'price' => 50, 'duration' => '4 hours'],
            ['name' => 'Adventure Activity', 'price' => 75, 'duration' => '5 hours'],
            ['name' => 'Sunset Cruise', 'price' => 60, 'duration' => '2 hours'],
        ];

        for ($i = 1; $i <= $days; $i++) {
            $hotel = $hotels[($i - 1) % count($hotels)];
            $activity = $activities[($i - 1) % count($activities)];

            $dayData = [
                'day' => $i,
                'title' => $this->getDayTitle($i, $days, $placeName),
                'places' => [
                    [
                        'place_id' => $package->place_id,
                        'name' => $placeName . ' - Main Attraction',
                        'visit_duration' => '2-3 hours',
                        'entry_ticket' => [
                            'required' => true,
                            'price' => 15 + ($i * 5),
                            'currency' => $package->currency ?? 'USD',
                            'booking_required' => $i > 1
                        ]
                    ]
                ],
                'hotel' => [
                    'name' => $hotel['name'],
                    'type' => $hotel['type'],
                    'check_in' => '14:00',
                    'check_out' => '12:00',
                    'price_per_night' => $hotel['price'],
                    'currency' => $package->currency ?? 'USD',
                    'amenities' => ['WiFi', 'Breakfast', 'Pool', 'Gym', 'Spa']
                ],
                'transport' => $this->getTransportForDay($i, $days, $package->currency ?? 'USD'),
                'activities' => [
                    [
                        'name' => $activity['name'],
                        'time' => $i === 1 ? '15:00' : '10:00',
                        'duration' => $activity['duration'],
                        'entry_ticket' => [
                            'price' => $activity['price'],
                            'currency' => $package->currency ?? 'USD',
                            'booking_required' => true
                        ],
                        'description' => 'Experience the best of ' . $placeName . ' with this amazing ' . strtolower($activity['name'])
                    ]
                ],
                'meals' => [
                    'breakfast' => $i === 1 ? 'Not included' : 'Included at hotel',
                    'lunch' => 'Local restaurant (own expense)',
                    'dinner' => ($i === 1 || $i === $days) ? 'Special dinner included' : 'Not included'
                ],
                'notes' => $this->getNotesForDay($i, $days)
            ];

            // Add extra places for middle days
            if ($i > 1 && $i < $days) {
                $dayData['places'][] = [
                    'place_id' => $package->place_id,
                    'name' => $placeName . ' - Secondary Attraction',
                    'visit_duration' => '1-2 hours',
                    'entry_ticket' => [
                        'required' => false,
                        'price' => 0,
                        'currency' => $package->currency ?? 'USD',
                        'booking_required' => false
                    ]
                ];
            }

            $itinerary[] = $dayData;
        }

        return $itinerary;
    }

    /**
     * Get transport details for specific day
     *
     * @param int $day
     * @param int $totalDays
     * @param string $currency
     * @return array
     */
    private function getTransportForDay($day, $totalDays, $currency)
    {
        $transport = [];

        if ($day === 1) {
            // Arrival day - airport transfer
            $transport[] = [
                'type' => 'Airport Transfer',
                'mode' => 'Private Car',
                'from' => 'Airport',
                'to' => 'Hotel',
                'price' => 50,
                'currency' => $currency,
                'duration' => '45 minutes',
                'notes' => 'Driver will be waiting at arrivals with name board'
            ];
        } elseif ($day === $totalDays) {
            // Departure day - airport drop-off
            $transport[] = [
                'type' => 'Airport Drop-off',
                'mode' => 'Private Car',
                'from' => 'Hotel',
                'to' => 'Airport',
                'price' => 50,
                'currency' => $currency,
                'duration' => '45 minutes',
                'notes' => 'Pick up 3 hours before flight departure'
            ];
        } else {
            // Regular day - local transport
            $transport[] = [
                'type' => 'Local Transport',
                'mode' => 'Private Car with Driver',
                'from' => 'Hotel',
                'to' => 'Various Attractions',
                'price' => 40,
                'currency' => $currency,
                'duration' => 'Full day',
                'notes' => 'Driver available from 9 AM to 6 PM'
            ];
        }

        return $transport;
    }

    /**
     * Get appropriate title for day
     *
     * @param int $day
     * @param int $totalDays
     * @param string $placeName
     * @return string
     */
    private function getDayTitle($day, $totalDays, $placeName)
    {
        if ($day === 1) {
            return 'Arrival in ' . $placeName;
        } elseif ($day === $totalDays) {
            return 'Departure from ' . $placeName;
        } elseif ($day === 2) {
            return 'Exploring ' . $placeName;
        } else {
            return 'Day ' . $day . ' - ' . $placeName . ' Adventure';
        }
    }

    /**
     * Get notes for specific day
     *
     * @param int $day
     * @param int $totalDays
     * @return string
     */
    private function getNotesForDay($day, $totalDays)
    {
        if ($day === 1) {
            return 'Comfortable walking shoes recommended. Bring sunscreen and water. Check-in at hotel after 2 PM.';
        } elseif ($day === $totalDays) {
            return 'Hotel checkout by 12 PM. Keep important documents handy for airport.';
        } else {
            return 'Wear comfortable clothing. Camera recommended for photos. Stay hydrated throughout the day.';
        }
    }
}
