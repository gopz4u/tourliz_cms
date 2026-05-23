<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeCustomerFieldsNullableInBookingsTable extends Migration
{
    public function up()
    {
        \DB::statement("ALTER TABLE bookings MODIFY customer_name VARCHAR(255) NULL");
        \DB::statement("ALTER TABLE bookings MODIFY customer_email VARCHAR(255) NULL");
        \DB::statement("ALTER TABLE bookings MODIFY customer_phone VARCHAR(255) NULL");
        \DB::statement("ALTER TABLE bookings MODIFY number_of_people INT NULL");
        \DB::statement("ALTER TABLE bookings MODIFY package_price DECIMAL(10,2) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE bookings MODIFY customer_name VARCHAR(255) NOT NULL");
        \DB::statement("ALTER TABLE bookings MODIFY customer_email VARCHAR(255) NOT NULL");
        \DB::statement("ALTER TABLE bookings MODIFY customer_phone VARCHAR(255) NOT NULL");
        \DB::statement("ALTER TABLE bookings MODIFY number_of_people INT NOT NULL");
        \DB::statement("ALTER TABLE bookings MODIFY package_price DECIMAL(10,2) NOT NULL");
    }
}
