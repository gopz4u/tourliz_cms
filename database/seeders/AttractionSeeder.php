<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\Package;
use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $places = Place::all();
        $packages = Package::all();
        
        if ($places->isEmpty()) {
            $this->call(PlaceSeeder::class);
            $places = Place::all();
        }
        
        if ($packages->isEmpty()) {
            $this->call(PackageSeeder::class);
            $packages = Package::all();
        }

        // Helper functions
        $getPlaceId = function($slug) use ($places) {
            $place = $places->where('slug', $slug)->first();
            return $place ? $place->id : null;
        };
        
        $getPackageId = function($slug) use ($packages) {
            $package = $packages->where('slug', $slug)->first();
            return $package ? $package->id : null;
        };

        $attractions = [
            [
                'name' => 'Burj Khalifa Observation Deck',
                'slug' => 'burj-khalifa-observation-deck',
                'short_description' => 'Visit the world\'s tallest building and enjoy panoramic views of Dubai.',
                'description' => '<p>Experience breathtaking 360-degree views of Dubai from the 124th and 125th floors of Burj Khalifa. Includes multimedia presentations about Dubai\'s history.</p>',
                'place_id' => $getPlaceId('dubai'),
                'package_id' => $getPackageId('dubai-city-tour-burj-khalifa'),
                'price' => 2500,
                'offer_price' => 2000,
                'price_2_6' => 1200,
                'price_6_10' => 1500,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(5)->format('Y-m-d'),
                'total_pax' => 50,
                'status' => true,
            ],
            [
                'name' => 'Ubud Monkey Forest',
                'slug' => 'ubud-monkey-forest',
                'short_description' => 'Walk through a sacred forest inhabited by hundreds of long-tailed macaques.',
                'description' => '<p>Explore the sacred Monkey Forest Sanctuary in Ubud, home to over 700 Balinese long-tailed macaques. Visit ancient temples within the forest.</p>',
                'place_id' => $getPlaceId('bali'),
                'package_id' => $getPackageId('bali-5-days-4-nights-honeymoon'),
                'price' => 800,
                'offer_price' => 600,
                'price_2_6' => 400,
                'price_6_10' => 500,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(7)->format('Y-m-d'),
                'total_pax' => 100,
                'status' => true,
            ],
            [
                'name' => 'Marina Bay Sands SkyPark',
                'slug' => 'marina-bay-sands-skypark',
                'short_description' => 'Visit the iconic infinity pool and observation deck at Marina Bay Sands.',
                'description' => '<p>Enjoy stunning views of Singapore from the SkyPark Observation Deck. Access to the world\'s most famous infinity pool (for hotel guests).</p>',
                'place_id' => $getPlaceId('singapore'),
                'package_id' => $getPackageId('singapore-universal-studios'),
                'price' => 2000,
                'offer_price' => 1700,
                'price_2_6' => 1000,
                'price_6_10' => 1300,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(4)->format('Y-m-d'),
                'total_pax' => 60,
                'status' => true,
            ],
            [
                'name' => 'Underwater Restaurant Experience',
                'slug' => 'maldives-underwater-restaurant',
                'short_description' => 'Dine in an underwater restaurant surrounded by marine life.',
                'description' => '<p>Experience fine dining in an underwater restaurant with panoramic views of the coral reef. Includes 5-course meal with wine pairing.</p>',
                'place_id' => $getPlaceId('maldives'),
                'package_id' => $getPackageId('maldives-water-villa-stay'),
                'price' => 15000,
                'offer_price' => 12000,
                'price_2_6' => 8000,
                'price_6_10' => 10000,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(10)->format('Y-m-d'),
                'total_pax' => 20,
                'status' => true,
            ],
            [
                'name' => 'Grand Palace & Wat Pho Tour',
                'slug' => 'bangkok-grand-palace-wat-pho',
                'short_description' => 'Visit Thailand\'s most sacred temples and royal palace.',
                'description' => '<p>Explore the magnificent Grand Palace complex and Wat Pho temple with the famous Reclining Buddha. Includes professional guide and transportation.</p>',
                'place_id' => $getPlaceId('thailand'),
                'package_id' => $getPackageId('thailand-bangkok-city-tour'),
                'price' => 1500,
                'offer_price' => 1200,
                'price_2_6' => 800,
                'price_6_10' => 1000,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(3)->format('Y-m-d'),
                'total_pax' => 40,
                'status' => true,
            ],
            [
                'name' => 'Petronas Twin Towers Skybridge',
                'slug' => 'petronas-twin-towers-skybridge',
                'short_description' => 'Walk across the skybridge connecting the iconic twin towers.',
                'description' => '<p>Visit the 41st and 42nd floors of the Petronas Twin Towers via the skybridge. Enjoy panoramic views of Kuala Lumpur cityscape.</p>',
                'place_id' => $getPlaceId('malaysia'),
                'package_id' => $getPackageId('malaysia-kl-genting'),
                'price' => 1800,
                'offer_price' => 1500,
                'price_2_6' => 900,
                'price_6_10' => 1200,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(6)->format('Y-m-d'),
                'total_pax' => 45,
                'status' => true,
            ],
            [
                'name' => 'Sigiriya Rock Fortress',
                'slug' => 'sigiriya-rock-fortress',
                'short_description' => 'Climb the ancient rock fortress with stunning frescoes.',
                'description' => '<p>Explore the ancient rock fortress of Sigiriya, a UNESCO World Heritage site. Climb to the top for breathtaking views and see ancient frescoes.</p>',
                'place_id' => $getPlaceId('sri-lanka'),
                'package_id' => $getPackageId('sri-lanka-cultural-triangle'),
                'price' => 3000,
                'offer_price' => 2500,
                'price_2_6' => 1500,
                'price_6_10' => 2000,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(8)->format('Y-m-d'),
                'total_pax' => 30,
                'status' => true,
            ],
            [
                'name' => 'Mount Everest Base Camp Helicopter Tour',
                'slug' => 'everest-base-camp-helicopter',
                'short_description' => 'Helicopter tour to Mount Everest Base Camp with mountain views.',
                'description' => '<p>Experience the thrill of flying to Mount Everest Base Camp by helicopter. Enjoy close-up views of the world\'s highest peak and surrounding mountains.</p>',
                'place_id' => $getPlaceId('nepal'),
                'package_id' => $getPackageId('nepal-kathmandu-pokhara'),
                'price' => 85000,
                'offer_price' => 75000,
                'price_2_6' => 45000,
                'price_6_10' => 55000,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(12)->format('Y-m-d'),
                'total_pax' => 6,
                'status' => true,
            ],
            [
                'name' => 'Dolphin Watching Cruise',
                'slug' => 'goa-dolphin-watching-cruise',
                'short_description' => 'Watch dolphins in their natural habitat on a boat cruise.',
                'description' => '<p>Enjoy a morning boat cruise to spot dolphins in the Arabian Sea. Includes breakfast and professional guide. Perfect for families.</p>',
                'place_id' => $getPlaceId('goa'),
                'package_id' => $getPackageId('goa-beach-holiday'),
                'price' => 1200,
                'offer_price' => 1000,
                'price_2_6' => 600,
                'price_6_10' => 800,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(5)->format('Y-m-d'),
                'total_pax' => 30,
                'status' => true,
            ],
            [
                'name' => 'Kerala Kathakali Dance Show',
                'slug' => 'kerala-kathakali-dance-show',
                'short_description' => 'Watch traditional Kathakali dance performance with elaborate costumes.',
                'description' => '<p>Experience the traditional art form of Kathakali with elaborate makeup, costumes, and expressive dance. Includes dinner and cultural explanation.</p>',
                'place_id' => $getPlaceId('kerala'),
                'package_id' => $getPackageId('kerala-backwaters-houseboat'),
                'price' => 800,
                'offer_price' => 650,
                'price_2_6' => 400,
                'price_6_10' => 500,
                'currency' => 'INR',
                'announcement_date' => now()->addDays(7)->format('Y-m-d'),
                'total_pax' => 50,
                'status' => true,
            ],
        ];

        foreach ($attractions as $attraction) {
            Attraction::firstOrCreate(
                ['slug' => $attraction['slug']],
                $attraction
            );
        }
    }
}
