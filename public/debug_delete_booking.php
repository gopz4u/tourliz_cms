<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "Fetching Booking ID 60 Raw Row:\n\n";

try {
    $row = DB::selectOne("SELECT * FROM bookings WHERE id = 60");
    if ($row) {
        echo "Row found:\n";
        print_r($row);
    } else {
        echo "Row with ID 60 not found.\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
