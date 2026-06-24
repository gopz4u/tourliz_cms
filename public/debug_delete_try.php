<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

header('Content-Type: text/plain');

try {
    echo "Attempting to delete B2CItinerary ID 16...\n";
    $itinerary = \App\Models\B2CItinerary::findOrFail(16);
    $itinerary->delete();
    echo "Successfully deleted B2CItinerary ID 16!\n";
} catch (\Exception $e) {
    echo "Error caught during deletion:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
