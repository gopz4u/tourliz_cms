<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurrencyExchangeRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $rates = CurrencyExchangeRate::orderBy('sort_order')->get();
            return response()->json($rates);
        }
        
        return view('admin.currency-rates.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:currency_exchange_rates,code',
            'name' => 'nullable|string|max:255',
            'exchange_rate' => 'required|numeric|min:0',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = isset($validated['is_active']) && ($validated['is_active'] == 1 || $validated['is_active'] === true || $validated['is_active'] === '1');

        $rate = CurrencyExchangeRate::create($validated);

        return response()->json($rate, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rate = CurrencyExchangeRate::findOrFail($id);
        return response()->json($rate);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rate = CurrencyExchangeRate::findOrFail($id);

        // Allow partial updates (for status toggle)
        if ($request->has('is_active') && !$request->has('exchange_rate')) {
            $validated = $request->validate([
                'is_active' => 'nullable',
            ]);
            $validated['is_active'] = isset($validated['is_active']) && ($validated['is_active'] == 1 || $validated['is_active'] === true || $validated['is_active'] === '1');
            $rate->update($validated);
            return response()->json($rate);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:3', Rule::unique('currency_exchange_rates')->ignore($rate->id)],
            'name' => 'nullable|string|max:255',
            'exchange_rate' => 'required|numeric|min:0',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = isset($validated['is_active']) && ($validated['is_active'] == 1 || $validated['is_active'] === true || $validated['is_active'] === '1');

        $rate->update($validated);

        return response()->json($rate);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rate = CurrencyExchangeRate::findOrFail($id);
        
        // Don't allow deleting MYR (base currency)
        if ($rate->code === 'MYR') {
            return response()->json([
                'error' => 'Cannot delete base currency (MYR)'
            ], 400);
        }
        
        $rate->delete();

        return response()->json(['message' => 'Currency exchange rate deleted successfully']);
    }

    /**
     * Bulk update exchange rates
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'rates' => 'required|array',
            'rates.*.id' => 'required|exists:currency_exchange_rates,id',
            'rates.*.exchange_rate' => 'required|numeric|min:0',
        ]);

        foreach ($validated['rates'] as $rateData) {
            $rate = CurrencyExchangeRate::findOrFail($rateData['id']);
            $rate->update(['exchange_rate' => $rateData['exchange_rate']]);
        }

        return response()->json(['message' => 'Exchange rates updated successfully']);
    }
}
