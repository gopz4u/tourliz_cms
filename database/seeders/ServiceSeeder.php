<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $places = Place::all();
        if ($places->isEmpty()) {
            $this->call(PlaceSeeder::class);
            $places = Place::all();
        }

        // Helper function to get place ID
        $getPlaceId = function($slug) use ($places) {
            $place = $places->where('slug', $slug)->first();
            return $place ? $place->id : null;
        };

        $services = [
            [
                'name' => 'Dubai Airport Transfer Service',
                'slug' => 'dubai-airport-transfer',
                'short_description' => 'Comfortable airport pickup and drop service in Dubai.',
                'description' => '<p>Professional airport transfer service with air-conditioned vehicles. Available 24/7 for Dubai International Airport and Al Maktoum Airport.</p>',
                'place_id' => $getPlaceId('dubai'),
                'category' => 'Airport Pickup',
                'price' => 2500,
                'price_2_6' => 1500,
                'price_6_10' => 1800,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(2)->format('Y-m-d'),
                'total_pax' => 4,
                'vehicle_type' => 'Sedan/SUV',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Bali Private Car Rental',
                'slug' => 'bali-private-car-rental',
                'short_description' => 'Hire a private car with driver for exploring Bali.',
                'description' => '<p>Rent a private car with English-speaking driver for 8-10 hours. Perfect for exploring Bali at your own pace. Includes fuel and parking.</p>',
                'place_id' => $getPlaceId('bali'),
                'category' => 'Transport',
                'price' => 3500,
                'price_2_6' => 2000,
                'price_6_10' => 2500,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(4)->format('Y-m-d'),
                'total_pax' => 6,
                'vehicle_type' => 'SUV/Minivan',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Singapore MRT Tourist Pass',
                'slug' => 'singapore-mrt-tourist-pass',
                'short_description' => 'Unlimited MRT and bus travel for tourists.',
                'description' => '<p>Get unlimited rides on Singapore\'s MRT and public buses with a tourist pass. Valid for 1, 2, or 3 days. Perfect for exploring the city.</p>',
                'place_id' => $getPlaceId('singapore'),
                'category' => 'Transport',
                'price' => 1200,
                'price_2_6' => 800,
                'price_6_10' => 1000,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(3)->format('Y-m-d'),
                'total_pax' => 1,
                'ticket_count' => 1,
                'ticket_name' => 'Tourist Pass',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Maldives Speedboat Transfer',
                'slug' => 'maldives-speedboat-transfer',
                'short_description' => 'Speedboat transfer to resort islands in the Maldives.',
                'description' => '<p>Comfortable speedboat transfer from Male airport to your resort island. Includes luggage handling and refreshments during the journey.</p>',
                'place_id' => $getPlaceId('maldives'),
                'category' => 'Transport',
                'price' => 8000,
                'price_2_6' => 5000,
                'price_6_10' => 6000,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(8)->format('Y-m-d'),
                'total_pax' => 20,
                'vehicle_type' => 'Speedboat',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Bangkok Tuk-Tuk Tour',
                'slug' => 'bangkok-tuk-tuk-tour',
                'short_description' => 'Explore Bangkok in a traditional tuk-tuk with guide.',
                'description' => '<p>Experience Bangkok like a local in a traditional tuk-tuk. Visit temples, markets, and local neighborhoods with an experienced guide.</p>',
                'place_id' => $getPlaceId('thailand'),
                'category' => 'Transport',
                'price' => 2000,
                'price_2_6' => 1200,
                'price_6_10' => 1500,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(2)->format('Y-m-d'),
                'total_pax' => 3,
                'vehicle_type' => 'Tuk-Tuk',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Kuala Lumpur Hotel Booking',
                'slug' => 'kuala-lumpur-hotel-booking',
                'short_description' => '4-star hotel accommodation in KL city center.',
                'description' => '<p>Stay at a centrally located 4-star hotel in Kuala Lumpur. Includes breakfast, WiFi, and access to hotel facilities. Close to shopping and attractions.</p>',
                'place_id' => $getPlaceId('malaysia'),
                'category' => 'Hotels',
                'price' => 4500,
                'price_2_6' => 2500,
                'price_6_10' => 3000,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(5)->format('Y-m-d'),
                'total_pax' => 2,
                'star_rating' => 4,
                'accommodation_type' => 'Hotel',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sri Lanka Train Journey - Kandy to Ella',
                'slug' => 'sri-lanka-train-kandy-ella',
                'short_description' => 'Scenic train journey through tea plantations.',
                'description' => '<p>Experience one of the world\'s most beautiful train journeys from Kandy to Ella. Pass through tea plantations, mountains, and waterfalls.</p>',
                'place_id' => $getPlaceId('sri-lanka'),
                'category' => 'Transport',
                'price' => 1500,
                'price_2_6' => 900,
                'price_6_10' => 1200,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(6)->format('Y-m-d'),
                'total_pax' => 1,
                'ticket_count' => 1,
                'ticket_name' => 'Train Ticket',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Nepal Domestic Flight - Kathmandu to Pokhara',
                'slug' => 'nepal-flight-kathmandu-pokhara',
                'short_description' => 'Scenic mountain flight from Kathmandu to Pokhara.',
                'description' => '<p>Enjoy breathtaking views of the Himalayas on a domestic flight from Kathmandu to Pokhara. See Mount Everest and other peaks from above.</p>',
                'place_id' => $getPlaceId('nepal'),
                'category' => 'Transport',
                'price' => 12000,
                'price_2_6' => 7000,
                'price_6_10' => 8500,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(10)->format('Y-m-d'),
                'total_pax' => 1,
                'ticket_count' => 1,
                'ticket_name' => 'Flight Ticket',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Goa Water Sports Package',
                'slug' => 'goa-water-sports-package',
                'short_description' => 'Enjoy various water sports activities in Goa.',
                'description' => '<p>Experience thrilling water sports including parasailing, jet skiing, banana boat ride, and speed boating. All equipment and safety gear included.</p>',
                'place_id' => $getPlaceId('goa'),
                'category' => 'Other Services',
                'price' => 3500,
                'price_2_6' => 2000,
                'price_6_10' => 2500,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(4)->format('Y-m-d'),
                'total_pax' => 1,
                'ticket_count' => 1,
                'ticket_name' => 'Water Sports Pass',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Kerala Ayurvedic Spa Treatment',
                'slug' => 'kerala-ayurvedic-spa',
                'short_description' => 'Traditional Ayurvedic spa and wellness treatment.',
                'description' => '<p>Experience authentic Ayurvedic spa treatments including Abhyanga massage, Shirodhara, and herbal steam bath. Includes consultation with Ayurvedic doctor.</p>',
                'place_id' => $getPlaceId('kerala'),
                'category' => 'Other Services',
                'price' => 4000,
                'price_2_6' => 2500,
                'price_6_10' => 3000,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(6)->format('Y-m-d'),
                'total_pax' => 1,
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            // Use DB::table to avoid soft delete issues if deleted_at column doesn't exist
            $exists = \DB::table('services')->where('slug', $service['slug'])->exists();
            if (!$exists) {
                \DB::table('services')->insert(array_merge($service, [
                    'gallery' => json_encode([]),
                    'icon' => null,
                    'sort_order' => 0,
                    'meta_title' => null,
                    'meta_description' => null,
                    'meta_keywords' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
