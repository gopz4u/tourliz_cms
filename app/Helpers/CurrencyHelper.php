<?php

namespace App\Helpers;

use App\Models\CurrencyExchangeRate;

class CurrencyHelper
{
    /**
     * Convert amount from one currency to another
     */
    public static function convert($amount, $fromCurrency, $toCurrency)
    {
        return CurrencyExchangeRate::convert($amount, $fromCurrency, $toCurrency);
    }

    public static function format($amount, $currency = 'MYR')
    {
        $symbols = [
            'INR' => '₹',
            'USD' => '$',
            'MYR' => 'RM',
            'SGD' => 'S$',
            'AED' => 'AED'
        ];

        $symbol = $symbols[$currency] ?? $currency;
        return $symbol . ' ' . number_format($amount, 2);
    }

    /**
     * Get all active currencies
     */
    public static function getActiveCurrencies()
    {
        return CurrencyExchangeRate::getActiveCurrencies();
    }

    /**
     * Get exchange rate for a currency
     */
    public static function getRate($currencyCode)
    {
        return CurrencyExchangeRate::getRate($currencyCode);
    }
}

