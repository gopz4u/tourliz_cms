<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\ItineraryHelper;

class CustomItinerary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'agency_id',
        'supplier_id',
        'title',
        'client_name',
        'destination_id',
        'start_date',
        'duration_days',
        'itinerary',
        'adults',
        'children_2_6',
        'children_6_11',
        'base_cost',
        'markup_percentage',
        'markup_amount',
        'total_price',
        'currency',
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
        'followed_up_at' => 'datetime',
        'next_followup_date' => 'date',
        'base_cost' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'user_id' => 'integer',
        'agency_id' => 'integer',
        'destination_id' => 'integer',
        'duration_days' => 'integer',
        'adults' => 'integer',
        'children_2_6' => 'integer',
        'children_6_11' => 'integer',
        'total_amount_received' => 'decimal:2',
    ];

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
        $costs = ItineraryHelper::calculateTotalCost($this->itinerary, $currency);

        // base_cost is the total trip cost for the entire group
        $this->base_cost = $costs['total'];

        if ($this->markup_percentage > 0) {
            $this->markup_amount = ((float) $this->base_cost * (float) $this->markup_percentage) / 100;
        } else {
            $this->markup_amount = 0;
        }

        // total_price is simply base_cost + markup_amount
        // We do NOT multiply by adults because the helper already accounts for pax-specific quantities
        $this->total_price = (float) $this->base_cost + (float) $this->markup_amount;

        return $this;
    }
}
