<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'country_code',
        'flag_emoji',
        'exchange_rate',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get exchange rate for a currency
     */
    public static function getRate($currencyCode)
    {
        $rate = self::where('code', $currencyCode)
            ->where('is_active', true)
            ->first();
        
        return $rate ? $rate->exchange_rate : 1.0;
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert($amount, $fromCurrency, $toCurrency)
    {
        // If same currency, return as is
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Get rates (all rates are relative to the anchor currency)
        $fromRate = self::getRate($fromCurrency);
        $toRate = self::getRate($toCurrency);

        // Convert: amount_in_base = amount * fromRate (where fromRate is "Base per Unit")
        // Then: converted_amount = amount_in_base / toRate
        
        $baseAmount = $amount * $fromRate;
        $convertedAmount = $baseAmount / $toRate;

        return round($convertedAmount, 2);
    }

    /**
     * Get all active currencies
     */
    public static function getActiveCurrencies()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
