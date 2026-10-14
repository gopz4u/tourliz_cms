<?php

namespace Tests\Feature;

use App\Models\GroupItinerary;
use Tests\TestCase;

class GroupItineraryPricingTest extends TestCase
{
    /** @test */
    public function group_children_are_added_without_reducing_the_adult_rate()
    {
        $itinerary = new GroupItinerary([
            'currency' => 'MYR',
            'adults' => 2,
            'children_2_6' => 1,
            'children_6_11' => 1,
            'markup_percentage' => 20,
            'itinerary' => [[
                'day' => 1,
                'hotels' => [['price_per_night' => 100, 'quantity' => 1]],
            ]],
        ]);

        $itinerary->calculatePricing();

        $this->assertSame(100.0, (float) $itinerary->base_cost);
        $this->assertSame(20.0, (float) $itinerary->markup_amount);
        $this->assertSame(165.0, (float) $itinerary->total_price);
        $this->assertSame(60.0, (float) $itinerary->total_price / (2 + 0.25 + 0.50));
    }
}
