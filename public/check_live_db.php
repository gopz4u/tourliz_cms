<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "--- All Bookings in DB table ---\n";
$all = DB::table('bookings')->get();
foreach ($all as $b) {
    echo "ID: {$b->id} | Name: {$b->name} | Price: {$b->price} | Total: {$b->total_amount} | Status: {$b->status} | Deleted At: " . ($b->deleted_at ?? 'NULL') . "\n";
}
