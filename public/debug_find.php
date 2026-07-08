<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$id = 25;
$itinerary = \App\Models\B2CItinerary::withTrashed()->find($id);

header('Content-Type: text/plain');

if ($itinerary) {
    echo "Found B2C: " . $itinerary->quote_id . "\n";
    print_r($itinerary->toArray());
} else {
    echo "Not found B2C with ID $id. Listing all B2C itineraries:\n";
    foreach (\App\Models\B2CItinerary::withTrashed()->get() as $b) {
        echo $b->id . " - " . $b->quote_id . " (Deleted: " . ($b->deleted_at ? 'Yes' : 'No') . ")\n";
    }
}
