<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryDayItem extends Model
{
    use HasFactory;

    protected $table = 'itinerary_day_items';

    protected $fillable = [
        'itinerary_day_id',
        'item_type',
        'item_id',
        'title',
        'description',
        'price',
        'cost',
        'supplier_id',
        'room_type',
        'start_time',
        'end_time',
        'sort_order',
    ];

    public function day()
    {
        return $this->belongsTo(ItineraryDay::class, 'itinerary_day_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'item_id');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'item_id');
    }

    public function ticket()
    {
        return $this->belongsTo(EntryTicket::class, 'item_id');
    }

    public function spot()
    {
        return $this->belongsTo(TouristSpot::class, 'item_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'item_id');
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class, 'item_id');
    }
}
