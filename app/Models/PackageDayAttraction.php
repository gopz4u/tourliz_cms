<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDayAttraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_day_id',
        'attraction_id'
    ];

    public function day()
    {
        return $this->belongsTo(PackageDay::class, 'package_day_id');
    }

    public function attraction()
    {
        return $this->belongsTo(EntryTicket::class, 'attraction_id');
    }
}
