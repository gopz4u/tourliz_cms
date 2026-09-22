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
        'highlights',
        'inclusions',
        'exclusions',
        'destination_id',
        'meal_plan'
    ];

    protected $casts = [
        'meal_plan' => 'array',
        'highlights' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
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
    public function fromJson($json, $asAssociative = true)
    {
        if (is_array($json)) {
            return $json;
        }

        if (!is_string($json) || trim($json) === '') {
            return [];
        }

        // Always decode as associative array (true) so blade views get arrays, not stdClass objects
        $decoded = json_decode($json, true);

        if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_null($decoded))) {
            return $decoded ?? [];
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
