<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Activity;
use App\Models\Transport;
use App\Models\EntryTicket;
use App\Models\Place;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $bali = Place::where('name', 'Bali')->first();
        if (!$bali)
            return;

        // 1. Hotels
        $hotel = Hotel::create([
            'name' => 'Grand Hyatt Bali',
            'place_id' => $bali->id,
            'star_rating' => 5,
            'address' => 'Nusa Dua, Bali',
            'description' => 'A luxury resort overlookin the Indian Ocean.',
        ]);

        HotelRoom::create([
            'hotel_id' => $hotel->id,
            'room_type' => 'Ocean View King',
            'base_price' => 250.00,
            'capacity' => 2,
        ]);

        HotelRoom::create([
            'hotel_id' => $hotel->id,
            'room_type' => 'Family Suite',
            'base_price' => 450.00,
            'capacity' => 4,
        ]);

        // 2. Activities
        Activity::create([
            'name' => 'Uluwatu Sunset Tour',
            'place_id' => $bali->id,
            'duration' => '4 hours',
            'base_price' => 45.00,
        ]);

        Activity::create([
            'name' => 'Mount Batur Trekking',
            'place_id' => $bali->id,
            'duration' => '8 hours',
            'base_price' => 60.00,
        ]);

        // 3. Transports
        Transport::create([
            'name' => 'Private Toyota Avanza',
            'vehicle_type' => 'SUV',
            'capacity' => 4,
            'base_price' => 35.00,
        ]);

        Transport::create([
            'name' => 'Luxury Alphard',
            'vehicle_type' => 'Minivan',
            'capacity' => 5,
            'base_price' => 120.00,
        ]);

        // 4. Entry Tickets
        EntryTicket::create([
            'attraction_name' => 'Tanah Lot Temple',
            'place_id' => $bali->id,
            'adult_price' => 10.00,
            'child_price' => 5.00,
        ]);

        EntryTicket::create([
            'attraction_name' => 'Sacred Monkey Forest',
            'place_id' => $bali->id,
            'adult_price' => 8.00,
            'child_price' => 4.00,
        ]);
    }
}
