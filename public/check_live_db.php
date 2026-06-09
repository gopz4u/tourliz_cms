<pre>
<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomItinerary;
use App\Models\B2CItinerary;

echo "--- Active B2B CustomItineraries ---\n";
foreach (CustomItinerary::all() as $x) {
    echo "ID: {$x->id} | Title: {$x->title} | Status: {$x->status} | Price: {$x->total_price} | Deleted At: " . ($x->deleted_at ?? 'NULL') . "\n";
}

echo "\n--- Trashed B2B CustomItineraries ---\n";
foreach (CustomItinerary::onlyTrashed()->get() as $x) {
    echo "ID: {$x->id} | Title: {$x->title} | Status: {$x->status} | Price: {$x->total_price} | Deleted At: " . ($x->deleted_at ?? 'NULL') . "\n";
}

echo "\n--- Active B2C B2CItineraries ---\n";
foreach (B2CItinerary::all() as $x) {
    echo "ID: {$x->id} | Title: {$x->title} | Status: {$x->status} | Price: {$x->total_price} | Deleted At: " . ($x->deleted_at ?? 'NULL') . "\n";
}

echo "\n--- Trashed B2C B2CItineraries ---\n";
foreach (B2CItinerary::onlyTrashed()->get() as $x) {
    echo "ID: {$x->id} | Title: {$x->title} | Status: {$x->status} | Price: {$x->total_price} | Deleted At: " . ($x->deleted_at ?? 'NULL') . "\n";
}
?>
</pre>
