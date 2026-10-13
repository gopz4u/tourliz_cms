<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupDeparture extends Model
{
    use HasFactory, SoftDeletes;

    public static function ensureTableSchema(): void
    {
        static $repaired = false;
        if ($repaired) {
            return;
        }

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('group_departures')) {
                \Illuminate\Support\Facades\Schema::create('group_departures', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('fixed_itinerary_id');
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->string('title');
                    $table->date('announcement_date')->nullable();
                    $table->date('departure_date');
                    $table->date('return_date')->nullable();
                    $table->decimal('adult_rate', 15, 2)->default(0);
                    $table->decimal('child_rate', 15, 2)->nullable();
                    $table->string('currency', 10)->default('MYR');
                    $table->unsignedInteger('capacity')->default(20);
                    $table->string('status')->default('announced');
                    $table->text('notes')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                    $table->index(['departure_date', 'status']);
                });
            }

            if (!\Illuminate\Support\Facades\Schema::hasTable('group_departure_customers')) {
                \Illuminate\Support\Facades\Schema::create('group_departure_customers', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('group_departure_id');
                    $table->string('name');
                    $table->string('phone', 40)->nullable();
                    $table->string('email')->nullable();
                    $table->string('passport_number', 100)->nullable();
                    $table->date('date_of_birth')->nullable();
                    $table->string('customer_type')->default('adult');
                    $table->unsignedInteger('pax')->default(1);
                    $table->decimal('rate', 15, 2)->default(0);
                    $table->decimal('amount_paid', 15, 2)->default(0);
                    $table->string('payment_status')->default('pending');
                    $table->string('booking_status')->default('reserved');
                    $table->text('notes')->nullable();
                    $table->timestamps();
                    $table->index(['group_departure_id', 'booking_status'], 'gdc_departure_status_idx');
                });
            }
            $repaired = true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('GroupDeparture table auto-create warning: ' . $e->getMessage());
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::ensureTableSchema();
    }

    protected $fillable = ['fixed_itinerary_id', 'user_id', 'title', 'announcement_date', 'departure_date', 'return_date', 'adult_rate', 'child_rate', 'currency', 'capacity', 'status', 'notes'];

    protected $casts = [
        'announcement_date' => 'date', 'departure_date' => 'date', 'return_date' => 'date',
        'adult_rate' => 'decimal:2', 'child_rate' => 'decimal:2', 'capacity' => 'integer',
    ];

    public function fixedItinerary() { return $this->belongsTo(FixedItinerary::class); }
    public function user() { return $this->belongsTo(Admin::class, 'user_id'); }
    public function customers() { return $this->hasMany(GroupDepartureCustomer::class); }

    public function getBookedPaxAttribute(): int
    {
        return (int) $this->customers()->where('booking_status', '!=', 'cancelled')->sum('pax');
    }

    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->capacity - $this->booked_pax);
    }
}
