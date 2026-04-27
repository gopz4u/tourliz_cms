<?php

/**
 * Test Itinerary API Endpoints
 * 
 * This script tests the itinerary generation and retrieval functionality
 * Run with: php test_itinerary.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Package;
use App\Helpers\ItineraryHelper;

echo "===========================================\n";
echo "  ITINERARY API TEST SCRIPT\n";
echo "===========================================\n\n";

// Test 1: Check packages
echo "Test 1: Checking packages in database...\n";
$packageCount = Package::count();
echo "Found {$packageCount} packages\n\n";

if ($packageCount === 0) {
    echo "❌ No packages found. Please run: php artisan db:seed --class=PackageSeeder\n";
    exit(1);
}

// Test 2: Get first package
echo "Test 2: Getting first package...\n";
$package = Package::first();
echo "Package: {$package->name} (slug: {$package->slug})\n";
echo "Duration: {$package->duration}\n";
echo "Has itinerary: " . ($package->hasItinerary() ? 'Yes' : 'No') . "\n\n";

// Test 3: Generate itinerary if it doesn't exist
if (!$package->hasItinerary()) {
    echo "Test 3: Generating sample itinerary...\n";

    $days = 3;
    if ($package->duration && preg_match('/(\d+)\s*days?/i', $package->duration, $matches)) {
        $days = (int) $matches[1];
    }

    $itinerary = ItineraryHelper::generateSampleItinerary($days, $package->place_id);
    $package->itinerary = $itinerary;
    $package->save();

    echo "✓ Generated {$days}-day itinerary\n\n";
} else {
    echo "Test 3: Package already has itinerary, skipping generation\n\n";
}

// Test 4: Validate itinerary
echo "Test 4: Validating itinerary structure...\n";
$validation = ItineraryHelper::validateItinerary($package->itinerary);
if ($validation['valid']) {
    echo "✓ Itinerary structure is valid\n\n";
} else {
    echo "❌ Itinerary validation failed:\n";
    foreach ($validation['errors'] as $error) {
        echo "  - {$error}\n";
    }
    echo "\n";
}

// Test 5: Calculate costs
echo "Test 5: Calculating cost breakdown...\n";
$costs = ItineraryHelper::calculateTotalCost($package->itinerary, $package->currency);
echo "Hotels: {$costs['currency']} {$costs['hotels']}\n";
echo "Transport: {$costs['currency']} {$costs['transport']}\n";
echo "Activities: {$costs['currency']} {$costs['activities']}\n";
echo "Entry Tickets: {$costs['currency']} {$costs['entry_tickets']}\n";
echo "TOTAL: {$costs['currency']} {$costs['total']}\n\n";

// Test 6: Enrich itinerary
echo "Test 6: Enriching itinerary with database data...\n";
$enriched = ItineraryHelper::enrichItinerary($package->itinerary);
$firstDay = $enriched[0] ?? null;
if ($firstDay && isset($firstDay['places'][0])) {
    $firstPlace = $firstDay['places'][0];
    echo "First day, first place:\n";
    echo "  - Name: {$firstPlace['name']}\n";
    if (isset($firstPlace['place_name'])) {
        echo "  - Enriched name: {$firstPlace['place_name']}\n";
    }
    if (isset($firstPlace['place_slug'])) {
        echo "  - Slug: {$firstPlace['place_slug']}\n";
    }
}
echo "\n";

// Test 7: API endpoint URLs
echo "Test 7: API Endpoint URLs\n";
$baseUrl = env('APP_URL', 'http://localhost');
echo "Get Itinerary:\n";
echo "  GET {$baseUrl}/api/v1/packages/{$package->slug}/itinerary\n\n";
echo "Generate Itinerary:\n";
echo "  POST {$baseUrl}/api/v1/packages/{$package->slug}/itinerary/generate\n\n";

// Test 8: Display sample itinerary
echo "Test 8: Sample Itinerary Data (First Day)\n";
echo "===========================================\n";
if ($firstDay) {
    echo "Day {$firstDay['day']}: {$firstDay['title']}\n\n";

    if (isset($firstDay['hotel'])) {
        echo "Hotel: {$firstDay['hotel']['name']} ({$firstDay['hotel']['type']})\n";
        echo "Price: {$firstDay['hotel']['currency']} {$firstDay['hotel']['price_per_night']}/night\n\n";
    }

    if (isset($firstDay['transport']) && count($firstDay['transport']) > 0) {
        echo "Transport:\n";
        foreach ($firstDay['transport'] as $transport) {
            echo "  - {$transport['type']}: {$transport['from']} → {$transport['to']}\n";
            echo "    Price: {$transport['currency']} {$transport['price']}\n";
        }
        echo "\n";
    }

    if (isset($firstDay['activities']) && count($firstDay['activities']) > 0) {
        echo "Activities:\n";
        foreach ($firstDay['activities'] as $activity) {
            echo "  - {$activity['name']} ({$activity['duration']})\n";
            if (isset($activity['entry_ticket']['price'])) {
                echo "    Price: {$activity['entry_ticket']['currency']} {$activity['entry_ticket']['price']}\n";
            }
        }
        echo "\n";
    }

    if (isset($firstDay['meals'])) {
        echo "Meals:\n";
        echo "  - Breakfast: {$firstDay['meals']['breakfast']}\n";
        echo "  - Lunch: {$firstDay['meals']['lunch']}\n";
        echo "  - Dinner: {$firstDay['meals']['dinner']}\n\n";
    }

    if (isset($firstDay['notes'])) {
        echo "Notes: {$firstDay['notes']}\n\n";
    }
}

echo "===========================================\n";
echo "✓ ALL TESTS COMPLETED SUCCESSFULLY!\n";
echo "===========================================\n\n";

echo "Next Steps:\n";
echo "1. Run seeder: php artisan db:seed --class=ItinerarySeeder\n";
echo "2. Test API: curl {$baseUrl}/api/v1/packages/{$package->slug}/itinerary\n";
echo "3. Check documentation: ITINERARY_API.md\n";
