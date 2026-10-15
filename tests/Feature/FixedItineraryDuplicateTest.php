<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\FixedItinerary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixedItineraryDuplicateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_duplicate_a_fixed_itinerary_and_customize_the_copy(): void
    {
        $admin = Admin::create([
            'name' => 'Admin', 'email' => 'duplicate@test.com',
            'password' => bcrypt('password'), 'role' => 'super_admin',
        ]);
        $original = FixedItinerary::create([
            'user_id' => $admin->id, 'title' => 'Malaysia Discovery',
            'duration_days' => 4, 'duration_nights' => 3,
            'short_description' => 'Original description',
            'days' => [['day_number' => 1, 'title' => 'Arrival', 'items' => []]],
            'fixed_price' => 1200, 'currency' => 'MYR', 'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.fixed-itineraries.duplicate', $original->id)
        );
        $copy = FixedItinerary::where('id', '!=', $original->id)->firstOrFail();

        $response->assertRedirect(route('admin.fixed-itineraries.edit', $copy->id));
        $this->assertSame('Malaysia Discovery (Copy)', $copy->title);
        $this->assertSame($original->days, $copy->days);
        $this->assertSame('1200.00', $copy->fixed_price);

        $copy->update(['title' => 'Customized Malaysia Trip']);
        $this->assertSame('Malaysia Discovery', $original->fresh()->title);
        $this->assertSame('Customized Malaysia Trip', $copy->fresh()->title);
    }
}
