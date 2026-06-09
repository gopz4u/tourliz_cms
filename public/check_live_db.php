<pre>
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

echo "\n--- All Bookings in DB table ---\n";
$all = DB::table('bookings')->get();
foreach ($all as $b) {
    echo "ID: {$b->id} | Name: " . substr(str_replace("\n", " ", $b->name ?? ''), 0, 20) . " | Price: {$b->price} | Total: {$b->total_amount} | Status: {$b->status} | Deleted At: " . ($b->deleted_at ?? 'NULL') . "\n";
}
?>
</pre>
