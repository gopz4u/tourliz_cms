<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDayHotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_day_id',
        'hotel_id',
        'room_type_id',
        'meal_plan_code',
        'is_primary'
    ];

    public function day()
    {
        return $this->belongsTo(PackageDay::class, 'package_day_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(HotelRoom::class, 'room_type_id');
    }
}
