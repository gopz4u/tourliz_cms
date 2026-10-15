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
            $checked = true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('B2C fixed-pricing schema repair failed: ' . $e->getMessage());
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::ensureFixedPricingSchema();
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
        'last_followup_whatsapp_at',
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
        'last_followup_whatsapp_at' => 'datetime',
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
        $this->total_price = (float) $this->fixed_price > 0
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
        return $this->quote_currency ?: ($this->currency ?: 'MYR');
    }

    public function convertForCustomer($amount): float
    {
        $converted = (float) $amount * max((float) ($this->exchange_rate ?: 1), 0.00000001);

        return $this->round_quote ? round($converted) : round($converted, 2);
    }

    public function getCustomerTotalPriceAttribute(): float
    {
        return $this->convertForCustomer($this->total_price);
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
