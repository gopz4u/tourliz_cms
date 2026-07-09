<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$itinerary = \App\Models\CustomItinerary::find(27);
if (!$itinerary) {
    echo "Itinerary 27 not found.";
    exit;
}

header('Content-Type: application/json');
echo json_encode([
    'id' => $itinerary->id,
    'title' => $itinerary->title,
    'total_price' => $itinerary->total_price,
    'base_cost' => $itinerary->base_cost,
    'markup_percentage' => $itinerary->markup_percentage,
    'markup_amount' => $itinerary->markup_amount,
    'adults' => $itinerary->adults,
    'children_2_6' => $itinerary->children_2_6,
    'children_6_11' => $itinerary->children_6_11,
    'itinerary_data' => json_decode($itinerary->itinerary_data, true)
], JSON_PRETTY_PRINT);
