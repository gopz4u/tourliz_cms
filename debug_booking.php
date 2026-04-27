<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$row = DB::table('bookings')->where('id', 5)->first();
if ($row) {
    echo "ID: " . $row->id . "\n";
    echo "Name: '" . ($row->name ?? 'NULL') . "'\n";
    echo "Email: '" . ($row->email ?? 'NULL') . "'\n";
    echo "Phone: '" . ($row->phone ?? 'NULL') . "'\n";
    echo "Customer Name: '" . ($row->customer_name ?? 'NULL') . "'\n";
    echo "Customer Email: '" . ($row->customer_email ?? 'NULL') . "'\n";
    echo "Customer Phone: '" . ($row->customer_phone ?? 'NULL') . "'\n";
    echo "Address: '" . ($row->address ?? 'NULL') . "'\n";
    echo "Customer Address: '" . ($row->customer_address ?? 'NULL') . "'\n";
} else {
    echo "Booking ID 5 not found.\n";
}
