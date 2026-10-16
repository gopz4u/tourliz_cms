<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedItinerary extends Model
{
    use HasFactory, SoftDeletes;

    public static function ensureTableSchema(): void
    {
        static $repaired = false;
        if ($repaired) {
            return;
        }

        if (!\Illuminate\Support\Facades\Schema::hasTable('fixed_itineraries')) {
            return;
        }

        try {
            \Illuminate\Support\Facades\Schema::table('fixed_itineraries', function (\Illuminate\Database\Schema\Blueprint $table) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'destination_id')) {
                    $table->unsignedBigInteger('destination_id')->nullable()->after('supplier_id');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'supplier_ids')) {
                    $table->json('supplier_ids')->nullable()->after('destination_id');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'duration_days')) {
                    $table->integer('duration_days')->default(1)->after('title');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'duration_nights')) {
                    $table->integer('duration_nights')->default(0)->after('duration_days');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'short_description')) {
                    $table->text('short_description')->nullable()->after('duration_nights');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'highlights')) {
                    $table->json('highlights')->nullable()->after('itinerary_description');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'inclusions')) {
                    $table->json('inclusions')->nullable()->after('highlights');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'exclusions')) {
                    $table->json('exclusions')->nullable()->after('inclusions');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'terms_conditions')) {
                    $table->text('terms_conditions')->nullable()->after('exclusions');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'days')) {
                    $table->json('days')->nullable()->after('terms_conditions');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'image')) {
                    $table->string('image')->nullable()->after('currency');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'operational_cost')) {
                    $table->decimal('operational_cost', 15, 2)->default(0)->after('fixed_price');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'markup_percentage')) {
                    $table->decimal('markup_percentage', 8, 2)->default(0)->after('operational_cost');
                }
            });
            $repaired = true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('FixedItinerary schema auto-repair warning: ' . $e->getMessage());
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::ensureTableSchema();

        static::saving(function ($model) {
            static::ensureTableSchema();

            try {
                $tableColumns = \Illuminate\Support\Facades\Schema::getColumnListing($model->getTable());
                if (!empty($tableColumns)) {
                    $raw = $model->getAttributes();
                    $filtered = array_intersect_key($raw, array_flip($tableColumns));
                    $model->setRawAttributes($filtered);
                }
            } catch (\Throwable $e) {
                // If column listing cannot be retrieved, continue normally
            }
        });
    }

    protected $fillable = [
        'user_id',
        'supplier_id',
        'supplier_ids',
        'destination_id',
        'country_ids',
        'title',
        'duration_days',
        'duration_nights',
        'short_description',
        'itinerary_description',
        'highlights',
        'inclusions',
        'exclusions',
        'terms_conditions',
        'days',
        'fixed_price',
        'operational_cost',
        'markup_percentage',
        'currency',
        'image',
        'status',
    ];

    protected $casts = [
        'supplier_ids' => 'array',
        'country_ids' => 'array',
        'highlights' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'days' => 'array',
        'fixed_price' => 'decimal:2',
        'operational_cost' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
    ];

    public function setSupplierIdsAttribute($value): void
    {
        $this->setSafeJsonAttribute('supplier_ids', $value);
    }

    public function getSupplierIdsAttribute($value): array
    {
        return $this->getSafeJsonAttribute($value);
    }

    public function setCountryIdsAttribute($value): void
    {
        $this->setSafeJsonAttribute('country_ids', $value);
    }

    public function getCountryIdsAttribute($value): array
    {
        return $this->getSafeJsonAttribute($value);
    }

    public function setHighlightsAttribute($value): void
    {
        $this->setSafeJsonAttribute('highlights', $value);
    }

    public function getHighlightsAttribute($value): array
    {
        return $this->getSafeJsonAttribute($value);
    }

    public function setInclusionsAttribute($value): void
    {
        $this->setSafeJsonAttribute('inclusions', $value);
    }

    public function getInclusionsAttribute($value): array
    {
        return $this->getSafeJsonAttribute($value);
    }

    public function setExclusionsAttribute($value): void
    {
        $this->setSafeJsonAttribute('exclusions', $value);
    }

    public function getExclusionsAttribute($value): array
    {
        return $this->getSafeJsonAttribute($value);
    }

    public function setDaysAttribute($value): void
    {
        $this->setSafeJsonAttribute('days', $value);
    }

    public function getDaysAttribute($value): array
    {
        return $this->getSafeJsonAttribute($value);
    }

    private function setSafeJsonAttribute(string $key, $value): void
    {
        $this->attributes[$key] = json_encode(
            is_array($value) ? $value : ($value ?? []),
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    private function getSafeJsonAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);

        return is_array($decoded) ? $decoded : [];
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getSuppliersAttribute()
    {
        $ids = $this->supplier_ids ?: [];
        if (empty($ids) && $this->supplier_id) {
            $ids = [(int) $this->supplier_id];
        }
        if (empty($ids)) {
            return collect();
        }
        return Supplier::whereIn('id', $ids)->get();
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function getCountriesAttribute()
    {
        $ids = $this->country_ids ?: [];
        if (empty($ids)) {
            return collect();
        }
        return Country::whereIn('id', $ids)->get();
    }

    public function getDurationAttribute(): string
    {
        $days = (int) ($this->duration_days ?: 1);
        $nights = (int) ($this->duration_nights ?: 0);
        return "{$days} Days / {$nights} Nights";
    }

    public function hasStructuredDays(): bool
    {
        return !empty($this->days) && is_array($this->days) && count($this->days) > 0;
    }

    public function getCurrencySymbolAttribute(): string
    {
        $symbols = [
            'MYR' => 'RM',
            'INR' => '₹',
            'USD' => '$',
            'SGD' => 'S$',
            'AED' => 'AED',
            'EUR' => '€',
            'GBP' => '£',
        ];
        return $symbols[$this->currency ?: 'MYR'] ?? ($this->currency ?: 'MYR');
    }

    public function getConvertedPricesAttribute(): array
    {
        $baseCurrency = $this->currency ?: 'MYR';
        $basePrice = (float) ($this->fixed_price ?? 0);
        $currencies = ['MYR', 'USD', 'INR'];
        
        $converted = [];
        foreach ($currencies as $cur) {
            $converted[$cur] = [
                'code' => $cur,
                'symbol' => $cur === 'INR' ? '₹' : ($cur === 'USD' ? '$' : 'RM'),
                'amount' => \App\Helpers\CurrencyHelper::convert($basePrice, $baseCurrency, $cur),
            ];
        }
        return $converted;
    }
}
