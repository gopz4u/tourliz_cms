<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedItinerary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'supplier_ids',
        'destination_id',
        'country_ids',
        'title',
        'duration_days',
        'duration_nights',
        'short_description',
        'itinerary_description',
        'highlights',
        'inclusions',
        'exclusions',
        'terms_conditions',
        'days',
        'fixed_price',
        'currency',
        'image',
        'status',
    ];

    protected $casts = [
        'supplier_ids' => 'array',
        'country_ids' => 'array',
        'highlights' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'days' => 'array',
        'fixed_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getSuppliersAttribute()
    {
        $ids = $this->supplier_ids ?: [];
        if (empty($ids) && $this->supplier_id) {
            $ids = [(int) $this->supplier_id];
        }
        if (empty($ids)) {
            return collect();
        }
        return Supplier::whereIn('id', $ids)->get();
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function getCountriesAttribute()
    {
        $ids = $this->country_ids ?: [];
        if (empty($ids)) {
            return collect();
        }
        return Country::whereIn('id', $ids)->get();
    }

    public function getDurationAttribute(): string
    {
        $days = (int) ($this->duration_days ?: 1);
        $nights = (int) ($this->duration_nights ?: 0);
        return "{$days} Days / {$nights} Nights";
    }

    public function hasStructuredDays(): bool
    {
        return !empty($this->days) && is_array($this->days) && count($this->days) > 0;
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
        $basePrice = (float) ($this->fixed_price ?? 0);
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

