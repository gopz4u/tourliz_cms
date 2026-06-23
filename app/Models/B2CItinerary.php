<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\ItineraryHelper;

class B2CItinerary extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'b2c_itineraries';

    protected $fillable = [
        'user_id',
        'destination_id',
        'supplier_id',
        'client_name',
        'email',
        'phone',
        'secondary_phone',
        'title',
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
        'total_amount_received',
        'payment_details',
        'followup_status',
        'followed_up_at',
        'next_followup_date',
        'lead_source',
        'special_requirements',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'itinerary' => 'array',
        'followed_up_at' => 'datetime',
        'next_followup_date' => 'date',
        'base_cost' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'adults' => 'integer',
        'children_2_6' => 'integer',
        'children_6_11' => 'integer',
        'total_amount_received' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function destination()
    {
        return $this->belongsTo(Country::class, 'destination_id');
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

        // total_price is simply base_cost + markup_amount
        $this->total_price = $this->base_cost + $this->markup_amount;

        return $this;
    }
}
