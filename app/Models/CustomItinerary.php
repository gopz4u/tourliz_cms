<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\ItineraryHelper;

class CustomItinerary extends Model
{
    use HasFactory, SoftDeletes;

    public static function ensureTravelDateSchema(): void
    {
        static $checked = false;
        $table = 'custom_itineraries';

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
            \Illuminate\Support\Facades\Log::warning('B2B travel-date schema repair failed: ' . $e->getMessage());
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::ensureTravelDateSchema();
    }

    protected $fillable = [
        'user_id',
        'agency_id',
        'supplier_id',
        'title',
        'short_description',
        'client_name',
        'destination_id',
        'country_ids',
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
        'currency',
        'quote_currency',
        'exchange_rate',
        'exchange_rate_date',
        'round_quote',
        'status',
        'payment_status',
        'followup_status',
        'followed_up_at',
        'next_followup_date',
        'total_amount_received',
        'payment_details',
        'notes',
        'quote_number', // Added for search/tracking
    ];

    /**
     * Get unique Quote ID: QT-YYYYMMDD-DEST-ID
     */
    public function getQuoteIdAttribute()
    {
        $date = $this->created_at->format('Ymd');
        $dest = strtoupper(substr($this->destination->name ?? 'LOC', 0, 3));
        return "QT-{$date}-{$dest}-" . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    protected $casts = [
        'start_date' => 'date',
        'itinerary' => 'array',
        'highlights' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'followed_up_at' => 'datetime',
        'next_followup_date' => 'date',
        'base_cost' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'user_id' => 'integer',
        'agency_id' => 'integer',
        'destination_id' => 'integer',
        'country_ids' => 'array',
        'duration_days' => 'integer',
        'adults' => 'integer',
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

    /**
     * Get the user (admin/creator) that owns the itinerary.
     */
    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Get the agency this itinerary is for.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the supplier (vendor) associated with the itinerary.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the country associated with the itinerary (stored in destination_id).
     */
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

    public function expenses()
    {
        return $this->hasMany(ItineraryExpense::class, 'itinerary_id')->where('itinerary_type', 'b2b');
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    /**
     * Get formatted itinerary with enriched data
     */
    public function getFormattedItineraryAttribute()
    {
        if (!$this->itinerary) {
            return [];
        }

        return ItineraryHelper::enrichItinerary($this->itinerary);
    }

    public function calculatePricing()
    {
        $currency = $this->currency ?: 'MYR';
        $enriched = ItineraryHelper::enrichItinerary($this->itinerary);
        $costs = ItineraryHelper::calculateTotalCost($enriched, $currency);

        // base_cost is the total trip cost for the entire group
        $this->base_cost = $costs['total'];

        if ($this->markup_percentage > 0) {
            $this->markup_amount = ((float) $this->base_cost * (float) $this->markup_percentage) / 100;
        } else {
            $this->markup_amount = 0;
        }

        $adultPackageSubtotal = (float) $this->base_cost + (float) $this->markup_amount;
        $adultCount = max(1, (int) $this->adults);
        $adultPerPerson = $adultPackageSubtotal / $adultCount;
        $childAddOn = ($adultPerPerson * 0.25 * max(0, (int) $this->children_2_6))
            + ($adultPerPerson * 0.50 * max(0, (int) $this->children_6_11));

        $this->total_price = round($adultPackageSubtotal + $childAddOn, 2);

        return $this;
    }

    public function getActiveCurrencyAttribute(): string
    {
        return $this->currency ?: 'MYR';
    }

    public function getCustomerCurrencyAttribute(): string
    {
        return $this->quote_currency ?: ($this->currency ?: 'MYR');
    }

    public function convertForCustomer($amount): float
    {
        $value = (float) $amount * max((float) ($this->exchange_rate ?: 1), 0.00000001);
        return $this->round_quote ? round($value) : round($value, 2);
    }

    public function getCustomerTotalPriceAttribute(): float
    {
        return $this->convertForCustomer($this->total_price);
    }

    /**
     * Reporting amount in MYR. For the normal MYR cost base this is the exact
     * saved base amount, rather than a rate recalculated today.
     */
    public function getMyrQuotedTotalAttribute(): float
    {
        $baseCurrency = strtoupper($this->currency ?: 'MYR');
        $customerCurrency = strtoupper($this->customer_currency ?: $baseCurrency);

        if ($customerCurrency === 'MYR') {
            return round($this->customer_total_price, 2);
        }

        if ($baseCurrency === 'MYR') {
            return round((float) $this->total_price, 2);
        }

        return (float) \App\Models\CurrencyExchangeRate::convert($this->customer_total_price, $customerCurrency, 'MYR');
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
