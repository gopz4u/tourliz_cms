<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaxAndPaymentToCustomItineraries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            // Pax Counts
            $table->integer('adults')->default(1)->after('itinerary');
            $table->integer('children_2_6')->default(0)->after('adults');
            $table->integer('children_6_11')->default(0)->after('children_2_6');

            // Payment Tracking
            $table->string('payment_status')->default('pending')->after('status'); // pending, partially_paid, paid, cancelled
            $table->decimal('total_amount_received', 15, 2)->default(0)->after('payment_status');
            $table->text('payment_details')->nullable()->after('total_amount_received');
        });
    }

    public function down()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->dropColumn(['adults', 'children_2_6', 'children_6_11', 'payment_status', 'total_amount_received', 'payment_details']);
        });
    }
}
