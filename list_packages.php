<?php
/**
 * Quick script to list all package slugs for testing
 * Run with: php list_packages.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Package;

echo "===========================================\n";
echo "  AVAILABLE PACKAGES\n";
echo "===========================================\n\n";

$packages = Package::select('id', 'name', 'slug')->orderBy('id')->get();

echo "Total packages: " . $packages->count() . "\n\n";

foreach ($packages as $package) {
    echo "ID: {$package->id}\n";
    echo "Name: {$package->name}\n";
    echo "Slug: {$package->slug}\n";
    echo "Itinerary URL: http://localhost:8000/api/v1/packages/{$package->slug}/itinerary\n";
    echo "-------------------------------------------\n";
}

echo "\n✅ To test an itinerary, copy one of the URLs above and paste in your browser!\n";
