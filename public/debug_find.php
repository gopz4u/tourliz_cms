<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$id = 25;
$itinerary = \App\Models\B2CItinerary::withTrashed()->find($id);

header('Content-Type: text/plain');

if ($itinerary) {
    echo "ID: " . $itinerary->id . "\n";
    echo "Quote ID: " . $itinerary->quote_id . "\n";
    echo "Adults: " . $itinerary->adults . "\n";
    echo "Children 2-6: " . $itinerary->children_2_6 . "\n";
    echo "Children 6-11: " . $itinerary->children_6_11 . "\n";
    echo "Total Price: " . $itinerary->total_price . "\n";
    echo "Base Cost: " . $itinerary->base_cost . "\n";
    echo "Markup: " . $itinerary->markup . "\n";
    echo "Markup Percentage: " . $itinerary->markup_percentage . "\n";
    echo "Selling Price (from DB): " . $itinerary->selling_price . "\n";
} else {
    echo "Not found B2C with ID $id. Listing all B2C itineraries:\n";
    foreach (\App\Models\B2CItinerary::withTrashed()->get() as $b) {
        echo $b->id . " - " . $b->quote_id . " (Deleted: " . ($b->deleted_at ? 'Yes' : 'No') . ")\n";
    }
}
