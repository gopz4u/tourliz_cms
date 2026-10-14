<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\CurrencyExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyExchangeRateTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::create([
            'name' => 'Currency Admin',
            'email' => 'currency@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);
    }

    /** @test */
    public function admin_can_manually_add_and_update_currency_rates()
    {
        $this->actingAs($this->admin)->postJson(route('admin.currency-rates.store'), [
            'code' => 'nzd',
            'name' => 'New Zealand Dollar',
            'exchange_rate' => 2.62,
            'is_active' => 1,
        ])->assertCreated()->assertJsonPath('code', 'NZD');

        $rate = CurrencyExchangeRate::where('code', 'NZD')->firstOrFail();
        $this->actingAs($this->admin)->postJson(route('admin.currency-rates.bulk-update'), [
            'rates' => [['id' => $rate->id, 'exchange_rate' => 4.81]],
        ])->assertOk();

        $this->assertSame(4.81, (float) $rate->fresh()->exchange_rate);
    }

    /** @test */
    public function manual_rates_must_be_greater_than_zero()
    {
        $this->actingAs($this->admin)->postJson(route('admin.currency-rates.store'), [
            'code' => 'USD',
            'name' => 'US Dollar',
            'exchange_rate' => 0,
            'is_active' => 1,
        ])->assertUnprocessable()->assertJsonValidationErrors('exchange_rate');
    }
}
