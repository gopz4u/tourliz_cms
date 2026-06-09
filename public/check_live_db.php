<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\CustomItinerary;
use App\Models\B2CItinerary;

header('Content-Type: text/plain');

echo "Active Bookings count: " . Booking::count() . "\n";
echo "Active Bookings Sum: " . Booking::sum('total_amount') . "\n";

echo "Trashed Bookings count: " . Booking::onlyTrashed()->count() . "\n";
echo "Trashed Bookings Sum: " . Booking::onlyTrashed()->sum('total_amount') . "\n";

echo "All Bookings count: " . Booking::withTrashed()->count() . "\n";
echo "All Bookings Sum: " . Booking::withTrashed()->sum('total_amount') . "\n";

echo "B2B sum: " . CustomItinerary::sum('total_price') . "\n";
echo "B2C sum: " . B2CItinerary::sum('total_price') . "\n";
