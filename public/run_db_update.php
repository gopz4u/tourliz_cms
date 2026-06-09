<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');
echo "Running database schema update:\n\n";

try {
    // 1. Drop malformed column
    try {
        DB::statement("ALTER TABLE bookings DROP COLUMN `bookings.deleted_at`");
        echo "Dropped column `bookings.deleted_at` successfully.\n";
    } catch (\Throwable $e) {
        echo "Note (bookings.deleted_at drop): " . $e->getMessage() . "\n";
    }

    // 2. Add standard deleted_at column
    if (!Schema::hasColumn('bookings', 'deleted_at')) {
        DB::statement("ALTER TABLE bookings ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
        echo "Added column `deleted_at` successfully.\n";
    } else {
        echo "Column `deleted_at` already exists.\n";
    }

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
