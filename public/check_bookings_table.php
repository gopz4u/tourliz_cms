<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "Describing bookings table:\n\n";

try {
    $columns = DB::select("DESCRIBE bookings");
    foreach ($columns as $column) {
        print_r($column);
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
