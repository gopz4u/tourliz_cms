<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function show($slug)
    {
        $itinerary = Itinerary::where('slug', $slug)
            ->where('itinerary_type', 'b2c')
            ->with(['destination', 'days.items', 'b2cDetail'])
            ->firstOrFail();

        // Check if it's published or if we want to allow drafts to be viewed by the customer
        // Usually quotations are specifically for the customer so they might be drafts.
        // We will just return the view.

        return view('public.quotation.show', compact('itinerary'));
    }
}
