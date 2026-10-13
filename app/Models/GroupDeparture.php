<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupDeparture extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['fixed_itinerary_id', 'user_id', 'title', 'announcement_date', 'departure_date', 'return_date', 'adult_rate', 'child_rate', 'currency', 'capacity', 'status', 'notes'];

    protected $casts = [
        'announcement_date' => 'date', 'departure_date' => 'date', 'return_date' => 'date',
        'adult_rate' => 'decimal:2', 'child_rate' => 'decimal:2', 'capacity' => 'integer',
    ];

    public function fixedItinerary() { return $this->belongsTo(FixedItinerary::class); }
    public function user() { return $this->belongsTo(Admin::class, 'user_id'); }
    public function customers() { return $this->hasMany(GroupDepartureCustomer::class); }

    public function getBookedPaxAttribute(): int
    {
        return (int) $this->customers()->where('booking_status', '!=', 'cancelled')->sum('pax');
    }

    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->capacity - $this->booked_pax);
    }
}
