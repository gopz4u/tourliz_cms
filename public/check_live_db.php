<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "Database Name: " . DB::connection()->getDatabaseName() . "\n";
echo "DB_HOST env: " . env('DB_HOST') . "\n";
echo "DB_DATABASE env: " . env('DB_DATABASE') . "\n";

echo "Bookings table count: " . DB::table('bookings')->count() . "\n";
echo "Bookings table count where deleted_at is null: " . DB::table('bookings')->whereNull('deleted_at')->count() . "\n";
echo "Bookings table count where deleted_at is not null: " . DB::table('bookings')->whereNotNull('deleted_at')->count() . "\n";

echo "\n--- All Bookings in DB table ---\n";
$all = DB::table('bookings')->get();
foreach ($all as $b) {
    echo "ID: {$b->id} | Name: {$b->name} | Total: {$b->total_amount} | Status: {$b->status} | Deleted At: " . ($b->deleted_at ?? 'NULL') . "\n";
}
