<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Get all active currency exchange rates
     */
    public function getRates()
    {
        $rates = CurrencyExchangeRate::where('is_active', true)
            ->orderBy('code')
            ->get(['code as currency_code', 'name as currency_name', 'exchange_rate']);
        
        return response()->json([
            'success' => true,
            'base_currency' => 'MYR',
            'rates' => $rates
        ]);
    }

    /**
     * Convert amount between currencies
     */
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        $converted = CurrencyExchangeRate::convert(
            $request->amount,
            $request->from,
            $request->to
        );

        return response()->json([
            'success' => true,
            'original_amount' => $request->amount,
            'original_currency' => $request->from,
            'converted_amount' => round($converted, 2),
            'converted_currency' => $request->to,
        ]);
    }
}

