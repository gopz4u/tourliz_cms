<?php
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    header('Content-Type: text/plain');
    echo "--- Recalculating All Database Itineraries ---\n\n";

    echo "--- B2C Itineraries ---\n";
    foreach (App\Models\B2CItinerary::all() as $it) {
        $oldPrice = $it->total_price;
        $it->calculatePricing()->save();
        if ($oldPrice != $it->total_price) {
            echo "ID {$it->id}: Old = {$oldPrice}, New = {$it->total_price}\n";
        }
    }

    echo "\n--- Custom (B2B) Itineraries ---\n";
    foreach (App\Models\CustomItinerary::all() as $it) {
        $oldPrice = $it->total_price;
        $it->calculatePricing()->save();
        if ($oldPrice != $it->total_price) {
            echo "ID {$it->id}: Old = {$oldPrice}, New = {$it->total_price}\n";
        }
    }

    echo "\n--- Group Itineraries ---\n";
    foreach (App\Models\GroupItinerary::all() as $it) {
        $oldPrice = $it->total_price;
        $it->calculatePricing()->save();
        if ($oldPrice != $it->total_price) {
            echo "ID {$it->id}: Old = {$oldPrice}, New = {$it->total_price}\n";
        }
    }

    echo "\nRecalculation complete!\n";
} catch (Throwable $e) {
    header('Content-Type: text/plain');
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
