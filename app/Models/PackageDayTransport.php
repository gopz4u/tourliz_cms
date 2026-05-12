<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDayTransport extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_day_id',
        'transport_id',
        'pickup_point',
        'drop_point'
    ];

    public function day()
    {
        return $this->belongsTo(PackageDay::class, 'package_day_id');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }
}
