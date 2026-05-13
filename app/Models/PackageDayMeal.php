<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDayMeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_day_id',
        'meal_id'
    ];

    public function day()
    {
        return $this->belongsTo(PackageDay::class, 'package_day_id');
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
