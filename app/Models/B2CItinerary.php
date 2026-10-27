<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\ItineraryHelper;

class B2CItinerary extends Model
{
    use HasFactory, SoftDeletes;

    public static function ensureFixedPricingSchema(): void
    {
        static $checked = false;
        if ($checked || !\Illuminate\Support\Facades\Schema::hasTable('b2c_itineraries')) {
            return;
        }

        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('b2c_itineraries', 'fixed_itinerary_id')) {
                \Illuminate\Support\Facades\Schema::table('b2c_itineraries', function ($table) {
                    $table->unsignedBigInteger('fixed_itinerary_id')->nullable()->index()->after('destination_id');
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('b2c_itineraries', 'fixed_price')) {
                \Illuminate\Support\Facades\Schema::table('b2c_itineraries', function ($table) {
                    $table->decimal('fixed_price', 15, 2)->nullable()->after('total_price');
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('b2c_itineraries', 'pricing_mode')) {
                \Illuminate\Support\Facades\Schema::table('b2c_itineraries', function ($table) {
                    $table->string('pricing_mode', 20)->default('live')->after('fixed_price');
                });
                \Illuminate\Support\Facades\DB::table('b2c_itineraries')
                    ->whereNotNull('fixed_price')->where('fixed_price', '>', 0)
                    ->update(['pricing_mode' => 'fixed']);
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('b2c_itineraries', 'fixed_price_currency')) {
                \Illuminate\Support\Facades\Schema::table('b2c_itineraries', function ($table) {
                    $table->string('fixed_price_currency', 10)->nullable()->after('fixed_price');
                });
                $rows = \Illuminate\Support\Facades\DB::table('b2c_itineraries')
                    ->whereNotNull('fixed_price')->where('fixed_price', '>', 0)->get();
                foreach ($rows as $row) {
                    $fixedCurrency = $row->currency ?: 'MYR';
                    \Illuminate\Support\Facades\DB::table('b2c_itineraries')->where('id', $row->id)->update([
                        'fixed_price_currency' => $fixedCurrency,
                        'currency' => 'MYR',
                        'quote_currency' => $fixedCurrency,
                    ]);
                }
            }
            $checked = true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('B2C fixed-pricing schema repair failed: ' . $e->getMessage());
        }
    }

    public static function ensureTravelDateSchema(): void
    {
        static $checked = false;
        $table = 'b2c_itineraries';

        if ($checked || !\Illuminate\Support\Facades\Schema::hasTable($table)) {
            return;
        }

        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn($table, 'travel_date_status')) {
                \Illuminate\Support\Facades\Schema::table($table, function ($blueprint) {
                    $blueprint->string('travel_date_status', 20)->default('tbd')->index()->after('start_date');
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn($table, 'preferred_travel_month')) {
                \Illuminate\Support\Facades\Schema::table($table, function ($blueprint) {
                    $blueprint->string('preferred_travel_month', 7)->nullable()->after('travel_date_status');
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn($table, 'travel_date_notes')) {
                \Illuminate\Support\Facades\Schema::table($table, function ($blueprint) {
                    $blueprint->string('travel_date_notes')->nullable()->after('preferred_travel_month');
                });
            }

            \Illuminate\Support\Facades\DB::table($table)
                ->whereNotNull('start_date')
                ->where('travel_date_status', 'tbd')
                ->update(['travel_date_status' => 'exact']);
            $checked = true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('B2C travel-date schema repair failed: ' . $e->getMessage());
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::ensureFixedPricingSchema();
        static::ensureTravelDateSchema();
    }

    protected $table = 'b2c_itineraries';

    protected $fillable = [
        'user_id',
        'destination_id',
        'fixed_itinerary_id',
        'country_ids',
        'supplier_id',
        'client_name',
        'email',
        'phone',
        'secondary_phone',
        'title',
        'short_description',
        'start_date',
        'travel_date_status',
        'preferred_travel_month',
        'travel_date_notes',
        'duration_days',
        'itinerary',
        'highlights',
        'inclusions',
        'exclusions',
        'terms_conditions',
        'adults',
        'children_2_6',
        'children_6_11',
        'base_cost',
        'markup_percentage',
        'markup_amount',
        'total_price',
        'fixed_price',
        'fixed_price_currency',
        'pricing_mode',
        'currency',
        'quote_currency',
        'exchange_rate',
        'exchange_rate_date',
        'round_quote',
        'status',
        'payment_status',
        'total_amount_received',
        'payment_details',
        'followup_status',
        'followed_up_at',
        'next_followup_date',
        'next_followup_at',
        'last_followup_whatsapp_at',
        'last_followup_notification_at',
        'last_followup_reminded_at',
        'followup_reminder_count',
        'followup_paused_until',
        'lead_source',
        'special_requirements',
        'notes',
    ];

    protected $casts = [
        'fixed_itinerary_id' => 'integer',
        'start_date' => 'date',
        'itinerary' => 'array',
        'highlights' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'followed_up_at' => 'datetime',
        'next_followup_date' => 'date',
        'next_followup_at' => 'datetime',
        'last_followup_whatsapp_at' => 'datetime',
        'last_followup_notification_at' => 'datetime',
        'last_followup_reminded_at' => 'datetime',
        'followup_paused_until' => 'datetime',
        'followup_reminder_count' => 'integer',
        'base_cost' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'fixed_price' => 'decimal:2',
        'adults' => 'integer',
        'country_ids' => 'array',
        'children_2_6' => 'integer',
        'children_6_11' => 'integer',
        'total_amount_received' => 'decimal:2',
        'exchange_rate' => 'decimal:8',
        'exchange_rate_date' => 'date',
        'round_quote' => 'boolean',
    ];

    public function getEffectiveTravelDateStatusAttribute(): string
    {
        return $this->travel_date_status ?: ($this->start_date ? 'exact' : 'tbd');
    }

    public function getTravelDateLabelAttribute(): string
    {
        if ($this->effective_travel_date_status === 'exact' && $this->start_date) {
            return $this->start_date->format('d M Y');
        }

        if ($this->effective_travel_date_status === 'flexible') {
            if ($this->preferred_travel_month) {
                try {
                    return \Carbon\Carbon::createFromFormat('Y-m', $this->preferred_travel_month)->format('M Y') . ' (Flexible)';
                } catch (\Throwable $e) {
                    return 'Flexible dates';
                }
            }
            return 'Flexible dates';
        }

        return 'Date TBD';
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function destination()
    {
        return $this->belongsTo(Country::class, 'destination_id');
    }

    public function getCountriesAttribute()
    {
        $ids = $this->country_ids ?: [];
        if (empty($ids)) {
            return collect();
        }
        return Country::whereIn('id', $ids)->get();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function expenses()
    {
        return $this->hasMany(ItineraryExpense::class, 'itinerary_id')->where('itinerary_type', 'b2c');
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    /**
     * Get unique Quote ID: B2C-YYYYMMDD-DEST-ID
     */
    public function getQuoteIdAttribute()
    {
        $date = $this->created_at->format('Ymd');
        $dest = strtoupper(substr($this->destination->name ?? 'LOC', 0, 3));
        return "B2C-{$date}-{$dest}-" . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    public function calculatePricing()
    {
        $currency = $this->currency ?: 'MYR';
        $enriched = ItineraryHelper::enrichItinerary($this->itinerary);
        $costs = ItineraryHelper::calculateTotalCost($enriched, $currency);

        // base_cost is the total trip cost for the entire group
        $this->base_cost = $costs['total'];

        if ($this->markup_percentage > 0) {
            $this->markup_amount = ($this->base_cost * $this->markup_percentage) / 100;
        } else {
            $this->markup_amount = 0;
        }

        // Establish the adult package price first. Children are additional
        // charges and must increase the final quote instead of reducing the
        // displayed adult per-person amount through weighted redistribution.
        $adultPackageSubtotal = (float) $this->base_cost + (float) $this->markup_amount;
        $adultCount = max(1, (int) $this->adults);
        $adultPerPerson = $adultPackageSubtotal / $adultCount;
        $childAddOn = ($adultPerPerson * 0.25 * max(0, (int) $this->children_2_6))
            + ($adultPerPerson * 0.50 * max(0, (int) $this->children_6_11));

        $calculatedTotal = round($adultPackageSubtotal + $childAddOn, 2);
        $this->total_price = $this->pricing_mode === 'fixed' && (float) $this->fixed_price > 0
            ? round((float) $this->fixed_price, 2)
            : $calculatedTotal;

        return $this;
    }

    public function getActiveCurrencyAttribute(): string
    {
        return $this->currency ?: 'MYR';
    }

    public function getCustomerCurrencyAttribute(): string
    {
        if ($this->pricing_mode === 'fixed' && (float) $this->fixed_price > 0) {
            return $this->fixed_price_currency ?: ($this->quote_currency ?: 'MYR');
        }

        return $this->quote_currency ?: ($this->currency ?: 'MYR');
    }

    public function convertForCustomer($amount): float
    {
        if ($this->customer_currency === ($this->currency ?: 'MYR')) {
            return $this->round_quote ? round((float) $amount) : round((float) $amount, 2);
        }

        $converted = (float) $amount * max((float) ($this->exchange_rate ?: 1), 0.00000001);

        return $this->round_quote ? round($converted) : round($converted, 2);
    }

    public function getCustomerTotalPriceAttribute(): float
    {
        if ($this->pricing_mode === 'fixed' && (float) $this->fixed_price > 0) {
            return round((float) $this->fixed_price, 2);
        }

        return $this->convertForCustomer($this->total_price);
    }

    /**
     * Reporting amount in MYR. Live customer quotes retain their MYR base
     * amount; fixed foreign-currency quotes use their saved proposal rate.
     */
    public function getMyrQuotedTotalAttribute(): float
    {
        $baseCurrency = strtoupper($this->currency ?: 'MYR');
        $customerCurrency = strtoupper($this->customer_currency ?: $baseCurrency);
        $customerTotal = $this->customer_total_price;

        if ($customerCurrency === 'MYR') {
            return round($customerTotal, 2);
        }

        if ($baseCurrency === 'MYR' && $this->pricing_mode !== 'fixed') {
            return round((float) $this->total_price, 2);
        }

        // New fixed-price proposals save this rate. Older proposals that have
        // no usable saved rate fall back to the configured MYR rate.
        if ($baseCurrency === 'MYR' && (float) $this->exchange_rate > 0 && (float) $this->exchange_rate !== 1.0) {
            return round($customerTotal / (float) $this->exchange_rate, 2);
        }

        return (float) \App\Models\CurrencyExchangeRate::convert($customerTotal, $customerCurrency, 'MYR');
    }

    public function getCustomerBaseCostAttribute(): float
    {
        return $this->convertForCustomer($this->base_cost);
    }

    public function getCustomerMarkupAmountAttribute(): float
    {
        return $this->convertForCustomer($this->markup_amount);
    }

    public function getCurrencySymbolAttribute(): string
    {
        $symbols = [
            'MYR' => 'RM',
            'INR' => '₹',
            'USD' => '$',
            'SGD' => 'S$',
            'AED' => 'AED',
            'EUR' => '€',
            'GBP' => '£',
        ];
        return $symbols[$this->currency ?: 'MYR'] ?? ($this->currency ?: 'MYR');
    }

    public function getConvertedPricesAttribute(): array
    {
        $baseCurrency = $this->currency ?: 'MYR';
        $basePrice = (float) ($this->total_price ?? 0);
        $currencies = ['MYR', 'USD', 'INR'];
        
        $converted = [];
        foreach ($currencies as $cur) {
            $converted[$cur] = [
                'code' => $cur,
                'symbol' => $cur === 'INR' ? '₹' : ($cur === 'USD' ? '$' : 'RM'),
                'amount' => \App\Helpers\CurrencyHelper::convert($basePrice, $baseCurrency, $cur),
            ];
        }
        return $converted;
    }
}
