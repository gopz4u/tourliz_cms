<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "--- Distinct itinerary_types in itinerary_expenses ---\n";
$types = DB::table('itinerary_expenses')->select('itinerary_type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))->groupBy('itinerary_type')->get();
foreach ($types as $t) {
    echo "Type: " . ($t->itinerary_type ?? 'NULL') . " | Count: {$t->count} | Total: {$t->total}\n";
}

echo "\n--- Active vs Trashed B2B Itineraries Expenses ---\n";
$activeB2BExpenses = DB::table('itinerary_expenses')
    ->join('custom_itineraries', 'custom_itineraries.id', '=', 'itinerary_expenses.itinerary_id')
    ->where('itinerary_expenses.itinerary_type', 'b2b')
    ->whereNull('custom_itineraries.deleted_at')
    ->sum('itinerary_expenses.amount');
$trashedB2BExpenses = DB::table('itinerary_expenses')
    ->join('custom_itineraries', 'custom_itineraries.id', '=', 'itinerary_expenses.itinerary_id')
    ->where('itinerary_expenses.itinerary_type', 'b2b')
    ->whereNotNull('custom_itineraries.deleted_at')
    ->sum('itinerary_expenses.amount');
echo "Active B2B Expenses: {$activeB2BExpenses}\n";
echo "Trashed B2B Expenses: {$trashedB2BExpenses}\n";

echo "\n--- Active vs Trashed B2C Itineraries Expenses ---\n";
$activeB2CExpenses = DB::table('itinerary_expenses')
    ->join('b2c_itineraries', 'b2c_itineraries.id', '=', 'itinerary_expenses.itinerary_id')
    ->where('itinerary_expenses.itinerary_type', 'b2c')
    ->whereNull('b2c_itineraries.deleted_at')
    ->sum('itinerary_expenses.amount');
$trashedB2CExpenses = DB::table('itinerary_expenses')
    ->join('b2c_itineraries', 'b2c_itineraries.id', '=', 'itinerary_expenses.itinerary_id')
    ->where('itinerary_expenses.itinerary_type', 'b2c')
    ->whereNotNull('b2c_itineraries.deleted_at')
    ->sum('itinerary_expenses.amount');
echo "Active B2C Expenses: {$activeB2CExpenses}\n";
echo "Trashed B2C Expenses: {$trashedB2CExpenses}\n";

echo "\n--- All Expenses in Table ---\n";
$allExp = DB::table('itinerary_expenses')->get();
foreach ($allExp as $e) {
    echo "ID: {$e->id} | Type: {$e->itinerary_type} | Itinerary ID: {$e->itinerary_id} | Amount: {$e->amount}\n";
}
