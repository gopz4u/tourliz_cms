<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\CustomItinerary;
use App\Models\B2CItinerary;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "Bookings table has deleted_at: " . (Schema::hasColumn('bookings', 'deleted_at') ? 'Yes' : 'No') . "\n";
echo "CustomItineraries table has deleted_at: " . (Schema::hasColumn('custom_itineraries', 'deleted_at') ? 'Yes' : 'No') . "\n";
echo "B2CItineraries table has deleted_at: " . (Schema::hasColumn('b2c_itineraries', 'deleted_at') ? 'Yes' : 'No') . "\n";

echo "\n--- CustomItinerary Stats ---\n";
echo "Active: " . CustomItinerary::count() . " | Sum: " . CustomItinerary::sum('total_price') . "\n";
if (Schema::hasColumn('custom_itineraries', 'deleted_at')) {
    echo "Trashed: " . CustomItinerary::onlyTrashed()->count() . " | Sum: " . CustomItinerary::onlyTrashed()->sum('total_price') . "\n";
}

echo "\n--- B2CItinerary Stats ---\n";
echo "Active: " . B2CItinerary::count() . " | Sum: " . B2CItinerary::sum('total_price') . "\n";
if (Schema::hasColumn('b2c_itineraries', 'deleted_at')) {
    echo "Trashed: " . B2CItinerary::onlyTrashed()->count() . " | Sum: " . B2CItinerary::onlyTrashed()->sum('total_price') . "\n";
}

echo "\n--- Booking Stats ---\n";
echo "Active: " . Booking::count() . " | Sum: " . Booking::sum('total_amount') . "\n";
echo "Trashed: " . Booking::onlyTrashed()->count() . " | Sum: " . Booking::onlyTrashed()->sum('total_amount') . "\n";

// Let's get the list of active bookings to see their details
echo "\n--- Active Bookings List ---\n";
foreach (Booking::all() as $b) {
    echo "ID: {$b->id} | Quote ID: {$b->quote_id} | Total Amount: {$b->total_amount} | Status: {$b->status} | Deleted At: {$b->deleted_at}\n";
}

echo "\n--- Trashed Bookings List ---\n";
foreach (Booking::onlyTrashed()->get() as $b) {
    echo "ID: {$b->id} | Quote ID: {$b->quote_id} | Total Amount: {$b->total_amount} | Status: {$b->status} | Deleted At: {$b->deleted_at}\n";
}
