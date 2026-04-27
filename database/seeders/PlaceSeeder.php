<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $places = [
            [
                'name' => 'Dubai',
                'slug' => 'dubai',
                'short_description' => 'The city of luxury and modern architecture',
                'description' => 'Dubai is a city and emirate in the United Arab Emirates known for luxury shopping, ultramodern architecture and a lively nightlife scene.',
                'location' => 'United Arab Emirates',
                'region' => 'Middle East',
                'rating' => 5,
                'featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Bali',
                'slug' => 'bali',
                'short_description' => 'Tropical paradise with beautiful beaches',
                'description' => 'Bali is an Indonesian island known for its forested volcanic mountains, iconic rice paddies, beaches and coral reefs.',
                'location' => 'Indonesia',
                'region' => 'Southeast Asia',
                'rating' => 5,
                'featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Singapore',
                'slug' => 'singapore',
                'short_description' => 'Modern city-state with diverse culture',
                'description' => 'Singapore is a global financial center with a tropical climate and multicultural population.',
                'location' => 'Singapore',
                'region' => 'Southeast Asia',
                'rating' => 5,
                'featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Maldives',
                'slug' => 'maldives',
                'short_description' => 'Pristine beaches and overwater bungalows',
                'description' => 'The Maldives is a tropical nation in the Indian Ocean composed of 26 ring-shaped atolls.',
                'location' => 'Maldives',
                'region' => 'South Asia',
                'rating' => 5,
                'featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Thailand',
                'slug' => 'thailand',
                'short_description' => 'Land of smiles with rich culture',
                'description' => 'Thailand is a Southeast Asian country known for tropical beaches, opulent royal palaces, ancient ruins and ornate temples.',
                'location' => 'Thailand',
                'region' => 'Southeast Asia',
                'rating' => 4,
                'featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Malaysia',
                'slug' => 'malaysia',
                'short_description' => 'Diverse culture and natural beauty',
                'description' => 'Malaysia is a Southeast Asian country occupying parts of the Malay Peninsula and the island of Borneo.',
                'location' => 'Malaysia',
                'region' => 'Southeast Asia',
                'rating' => 4,
                'featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Sri Lanka',
                'slug' => 'sri-lanka',
                'short_description' => 'Pearl of the Indian Ocean',
                'description' => 'Sri Lanka is an island nation south of India in the Indian Ocean, known for its diverse landscapes.',
                'location' => 'Sri Lanka',
                'region' => 'South Asia',
                'rating' => 4,
                'featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Nepal',
                'slug' => 'nepal',
                'short_description' => 'Home to Mount Everest',
                'description' => 'Nepal is a landlocked country in South Asia, known for Mount Everest and its rich cultural heritage.',
                'location' => 'Nepal',
                'region' => 'South Asia',
                'rating' => 4,
                'featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Goa',
                'slug' => 'goa',
                'short_description' => 'Beach paradise in India',
                'description' => 'Goa is a state in western India with coastlines stretching along the Arabian Sea.',
                'location' => 'India',
                'region' => 'South Asia',
                'rating' => 4,
                'featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Kerala',
                'slug' => 'kerala',
                'short_description' => 'God\'s Own Country',
                'description' => 'Kerala is a state on India\'s tropical Malabar Coast, known for its palm-lined beaches and backwaters.',
                'location' => 'India',
                'region' => 'South Asia',
                'rating' => 5,
                'featured' => true,
                'status' => true,
            ],
        ];

        foreach ($places as $place) {
            Place::firstOrCreate(
                ['slug' => $place['slug']],
                $place
            );
        }
    }
}
