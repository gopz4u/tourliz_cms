<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TouristSpot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'destination_id', 'supplier_id', 'description', 'image_url', 'is_active'];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
