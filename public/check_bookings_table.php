<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "Bookings table columns:\n";

try {
    $columns = DB::select("DESCRIBE bookings");
    $fields = array_map(function($c) { return $c->Field; }, $columns);
    echo implode(', ', $fields) . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
