<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryB2bDetail extends Model
{
    use HasFactory;

    protected $table = 'itinerary_b2b_details';

    protected $fillable = [
        'itinerary_id',
        'agency_id',
        'net_rate',
        'agent_rate',
        'markup_percentage',
        'markup_amount',
        'commission',
        'min_pax',
        'max_pax',
        'hotel_category',
        'room_category',
        'vehicle_category',
        'guide_required',
        'supplier_id',
        'validity_start',
        'validity_end',
        'operational_notes',
        'agent_notes',
    ];

    protected $casts = [
        'net_rate' => 'decimal:2',
        'agent_rate' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'guide_required' => 'boolean',
        'validity_start' => 'date',
        'validity_end' => 'date',
    ];

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
