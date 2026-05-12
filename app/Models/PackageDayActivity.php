<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDayActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_day_id',
        'activity_id'
    ];

    public function day()
    {
        return $this->belongsTo(PackageDay::class, 'package_day_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
