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
        'start_time',
        'end_time',
        'sort_order',
    ];

    public function day()
    {
        return $this->belongsTo(ItineraryDay::class, 'itinerary_day_id');
    }
}
