<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingParametersToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add-ons and Add-on Services
            $table->json('addons')->nullable()->after('notes'); // Selected package addons
            $table->json('addon_services')->nullable()->after('addons'); // Selected addon services
            
            // Customer additional details
            $table->string('customer_address')->nullable()->after('phone');
            $table->string('customer_city')->nullable()->after('customer_address');
            $table->string('customer_state')->nullable()->after('customer_city');
            $table->string('customer_country')->nullable()->after('customer_state');
            $table->string('customer_postal_code')->nullable()->after('customer_country');
            
            // Payment information
            $table->enum('payment_status', ['pending', 'paid', 'partially_paid', 'refunded'])->default('pending')->after('currency');
            $table->json('payment_details')->nullable()->after('payment_status'); // Payment method, transaction ID, amount, date, etc.
            
            // Contact method preference
            $table->enum('contact_method', ['email', 'whatsapp', 'phone', 'query'])->default('email')->after('payment_details');
            $table->string('whatsapp_number')->nullable()->after('contact_method');
            
            // Pricing breakdown
            $table->decimal('base_price', 12, 2)->nullable()->after('price');
            $table->decimal('addons_amount', 12, 2)->default(0)->after('base_price');
            $table->decimal('services_amount', 12, 2)->default(0)->after('addons_amount');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('services_amount');
            $table->decimal('total_amount', 12, 2)->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'addons',
                'addon_services',
                'customer_address',
                'customer_city',
                'customer_state',
                'customer_country',
                'customer_postal_code',
                'payment_status',
                'payment_details',
                'contact_method',
                'whatsapp_number',
                'base_price',
                'addons_amount',
                'services_amount',
                'discount_amount',
                'total_amount',
            ]);
        });
    }
}
