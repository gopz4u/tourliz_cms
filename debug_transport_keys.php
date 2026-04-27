<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\B2CItinerary;
use App\Models\CustomItinerary;

echo "--- Recent Itineraries Transport Check ---\n";
// Check both B2C and B2B
$ids = B2CItinerary::latest()->take(3)->pluck('id', 'title');

foreach ($ids as $title => $id) {
    $itinerary = B2CItinerary::find($id);
    echo "ID: $id ($title)\n";
    $days = $itinerary->itinerary ?? [];
    foreach ($days as $k => $day) {
        $dayNum = $k + 1;
        if (isset($day['transport'])) {
            echo "  Day $dayNum has 'transport' (singular): " . count($day['transport']) . " items\n";
        }
        if (isset($day['transports'])) {
            echo "  Day $dayNum has 'transports' (plural): " . count($day['transports']) . " items\n";
        }
    }
}
