<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itinerary extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'itineraries';

    protected $fillable = [
        'title',
        'slug',
        'itinerary_type', // 'b2c' or 'b2b'
        'destination_id',
        'duration_days',
        'duration_nights',
        'description',
        'highlights',
        'inclusions',
        'exclusions',
        'terms_conditions',
        'status', // draft, published, archived
        'is_published',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'highlights' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'is_published' => 'boolean',
        'duration_days' => 'integer',
        'duration_nights' => 'integer',
    ];

    /**
     * Encode the given value to JSON safely, handling non-UTF-8 characters gracefully.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Decode the given JSON back into an array or object safely, handling legacy string data gracefully.
     *
     * @param  string  $json
     * @param  bool  $asAssociative
     * @return mixed
     */
    public function fromJson($json, $asAssociative = false)
    {
        if (is_array($json)) {
            return $json;
        }

        if (!is_string($json) || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, $asAssociative);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Fallback for legacy plain text stored in database columns
        $text = preg_replace('/<\/(p|li|h[1-6]|div)>/i', "\n", $json);
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = strip_tags($text);
        $lines = explode("\n", $text);
        $clean = [];
        foreach ($lines as $line) {
            $lineStr = html_entity_decode($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (!mb_check_encoding($lineStr, 'UTF-8')) {
                $lineStr = mb_convert_encoding($lineStr, 'UTF-8', 'Windows-1252');
            }
            $lineStr = mb_scrub($lineStr, 'UTF-8');
            $lineStr = trim(str_replace(["\xC2\xA0", "\xA0", "&nbsp;"], ' ', $lineStr), " \t\n\r\0\x0B-•*");
            if ($lineStr !== '') {
                $clean[] = $lineStr;
            }
        }

        return $clean;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function days()
    {
        return $this->hasMany(ItineraryDay::class, 'itinerary_id')->orderBy('day_number');
    }

    public function b2bDetail()
    {
        return $this->hasOne(ItineraryB2bDetail::class, 'itinerary_id');
    }

    public function b2cDetail()
    {
        return $this->hasOne(ItineraryB2cDetail::class, 'itinerary_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // Scopes
    public function scopeB2C($query)
    {
        return $query->where('itinerary_type', 'b2c');
    }

    public function scopeB2B($query)
    {
        return $query->where('itinerary_type', 'b2b');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', 'published');
    }
}
