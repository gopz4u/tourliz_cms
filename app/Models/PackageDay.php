<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'day_number',
        'title',
        'description',
        'destination_id',
        'meal_plan'
    ];

    protected $casts = [
        'meal_plan' => 'array'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function hotels()
    {
        return $this->hasMany(PackageDayHotel::class);
    }

    public function transports()
    {
        return $this->hasMany(PackageDayTransport::class);
    }

    public function activities()
    {
        return $this->hasMany(PackageDayActivity::class);
    }

    public function attractions()
    {
        return $this->hasMany(PackageDayAttraction::class);
    }

    public function meals_list()
    {
        return $this->hasMany(PackageDayMeal::class);
    }
}
