<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryDay extends Model
{
    use HasFactory;

    protected $table = 'itinerary_days';

    protected $fillable = [
        'itinerary_id',
        'day_number',
        'title',
        'description',
        'highlights',
        'inclusions',
        'exclusions',
        'overnight_location',
    ];

    protected $casts = [
        'highlights' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
    ];

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }

    public function items()
    {
        return $this->hasMany(ItineraryDayItem::class, 'itinerary_day_id')->orderBy('sort_order');
    }
}
