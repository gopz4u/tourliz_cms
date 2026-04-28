<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_id', 'room_type', 'base_price', 'capacity', 'description', 'image', 'currency'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
