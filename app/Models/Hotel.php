<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'destination_id', 'supplier_id', 'star_rating', 'address', 'description', 'image', 'is_active'];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rooms()
    {
        return $this->hasMany(HotelRoom::class);
    }
}
