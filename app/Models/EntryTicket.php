<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntryTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['attraction_name', 'destination_id', 'supplier_id', 'adult_price', 'child_price', 'is_active', 'currency'];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
