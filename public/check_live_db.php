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
$b2b = CustomItinerary::all();
foreach ($b2b as $x) {
    echo "ID: {$x->id} | Title: {$x->title} | Status: {$x->status} | Followup: {$x->followup_status} | Price: {$x->total_price}\n";
}

echo "\n--- Active B2C B2CItineraries ---\n";
$b2c = B2CItinerary::all();
foreach ($b2c as $x) {
    echo "ID: {$x->id} | Title: {$x->title} | Status: {$x->status} | Followup: {$x->followup_status} | Price: {$x->total_price}\n";
}
?>
</pre>
