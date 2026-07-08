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
    echo "Base Cost (DB): " . $itinerary->base_cost . "\n";
    echo "Markup %: " . $itinerary->markup_percentage . "\n";
    echo "Total Price: " . $itinerary->total_price . "\n";
    
    $it = $itinerary->itinerary;
    $hotelSum = 0;
    $transportSum = 0;
    $activitySum = 0;
    $mealSum = 0;
    
    foreach ($it as $day) {
        if (!empty($day['hotels'])) {
            foreach ($day['hotels'] as $h) {
                $cost = (float)($h['price_per_night'] ?? 0) * (int)($h['quantity'] ?? 1) * (int)($h['nights'] ?? 1) + (float)($h['add_on_price'] ?? 0);
                $hotelSum += $cost;
                echo "Day " . $day['day'] . " Hotel: " . ($h['name'] ?? 'N/A') . " = $cost\n";
            }
        }
        if (!empty($day['hotel']['name'])) {
            $h = $day['hotel'];
            $cost = (float)($h['price_per_night'] ?? 0) * (int)($h['quantity'] ?? 1) * (int)($h['nights'] ?? 1) + (float)($h['add_on_price'] ?? 0);
            $hotelSum += $cost;
            echo "Day " . $day['day'] . " Hotel Single: " . $h['name'] . " = $cost\n";
        }
        if (!empty($day['transport'])) {
            foreach ($day['transport'] as $t) {
                $cost = (float)($t['price'] ?? 0);
                $transportSum += $cost;
                echo "Day " . $day['day'] . " Transport: " . ($t['name'] ?? 'N/A') . " = $cost\n";
            }
        }
        if (!empty($day['activities'])) {
            foreach ($day['activities'] as $act) {
                $ticket = $act['entry_ticket'] ?? [];
                $cost = ((float)($ticket['adult_price'] ?? 0) * (int)($ticket['adult_qty'] ?? 0)) +
                        ((float)($ticket['child_2_6_price'] ?? 0) * (int)($ticket['child_2_6_qty'] ?? 0)) +
                        ((float)($ticket['child_6_11_price'] ?? 0) * (int)($ticket['child_6_11_qty'] ?? 0));
                $activitySum += $cost;
                echo "Day " . $day['day'] . " Activity: " . ($act['name'] ?? 'N/A') . " = $cost\n";
            }
        }
        if (!empty($day['places'])) {
            foreach ($day['places'] as $p) {
                $ticket = $p['entry_ticket'] ?? [];
                $cost = ((float)($ticket['adult_price'] ?? 0) * (int)($ticket['adult_qty'] ?? 0)) +
                        ((float)($ticket['child_2_6_price'] ?? 0) * (int)($ticket['child_2_6_qty'] ?? 0)) +
                        ((float)($ticket['child_6_11_price'] ?? 0) * (int)($ticket['child_6_11_qty'] ?? 0));
                $activitySum += $cost;
                echo "Day " . $day['day'] . " Place: " . ($p['attraction_name'] ?? 'N/A') . " = $cost\n";
            }
        }
        if (!empty($day['spots'])) {
            foreach ($day['spots'] as $s) {
                $cost = (float)($s['hours'] ?? 0) * (float)($s['price_per_hour'] ?? 0);
                $activitySum += $cost;
                echo "Day " . $day['day'] . " Spot: " . ($s['name'] ?? 'N/A') . " = $cost\n";
            }
        }
        if (!empty($day['meals'])) {
            foreach ($day['meals'] as $m) {
                $cost = (float)($m['price'] ?? 0) * (float)($m['quantity'] ?? 1);
                $mealSum += $cost;
                echo "Day " . $day['day'] . " Meal: " . ($m['name'] ?? 'N/A') . " = $cost\n";
            }
        }
    }
    
    echo "Calculated Sums:\n";
    echo "  Hotels: $hotelSum\n";
    echo "  Transport: $transportSum\n";
    echo "  Activities/Places/Spots: $activitySum\n";
    echo "  Meals: $mealSum\n";
    echo "  Total Sum (Base Cost): " . ($hotelSum + $transportSum + $activitySum + $mealSum) . "\n";
} else {
    echo "Not found B2C with ID $id. Listing all B2C itineraries:\n";
    foreach (\App\Models\B2CItinerary::withTrashed()->get() as $b) {
        echo $b->id . " - " . $b->quote_id . " (Deleted: " . ($b->deleted_at ? 'Yes' : 'No') . ")\n";
    }
}
