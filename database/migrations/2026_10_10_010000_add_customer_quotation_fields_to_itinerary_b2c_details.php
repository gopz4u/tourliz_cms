<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerQuotationFieldsToItineraryB2cDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itinerary_b2c_details', function (Blueprint $table) {
            $table->boolean('is_template')->default(true)->after('itinerary_id');
            $table->string('quotation_reference')->nullable()->unique()->after('is_template');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete()->after('quotation_reference');
            $table->string('customer_name')->nullable()->after('lead_id');
            $table->string('customer_whatsapp')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_whatsapp');
            $table->date('travel_date_start')->nullable()->after('customer_email');
            $table->date('travel_date_end')->nullable()->after('travel_date_start');
            $table->integer('adults')->default(2)->after('travel_date_end');
            $table->integer('children')->default(0)->after('adults');
            $table->integer('rooms')->default(1)->after('children');
            $table->string('lead_source')->nullable()->after('rooms');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('admins')->nullOnDelete()->after('lead_source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itinerary_b2c_details', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropForeign(['assigned_staff_id']);
            $table->dropColumn([
                'is_template',
                'quotation_reference',
                'lead_id',
                'customer_name',
                'customer_whatsapp',
                'customer_email',
                'travel_date_start',
                'travel_date_end',
                'adults',
                'children',
                'rooms',
                'lead_source',
                'assigned_staff_id'
            ]);
        });
    }
}
