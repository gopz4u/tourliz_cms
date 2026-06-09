<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\Package;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "Debugging Booking Deletion (No Transactions):\n\n";

try {
    // 1. Check database connection
    DB::connection()->getPdo();
    echo "Database connection successful.\n";
    
    // 2. Find a package
    $package = Package::first();
    if (!$package) {
        echo "No packages found in the database. Cannot create dummy booking.\n";
    } else {
        echo "Found Package ID: {$package->id}\n";
        
        // 3. Create a dummy booking
        echo "Creating dummy booking...\n";
        $dummy = Booking::create([
            'package_id' => $package->id,
            'name' => 'Delete Test Dummy',
            'email' => 'dummy@example.com',
            'phone' => '1234567890',
            'travel_date' => date('Y-m-d'),
            'adults' => 1,
            'status' => 'pending',
            'followup_status' => 'leads',
            'total_amount' => 100.00,
        ]);
        echo "Dummy booking created with ID: {$dummy->id}\n";
        
        // 4. Try deleting the dummy booking
        echo "Attempting to delete dummy booking...\n";
        $dummy->delete();
        echo "SUCCESS: Dummy booking deleted successfully!\n\n";
    }
    
    // 5. Try deleting the last booking (real test)
    $booking = Booking::orderBy('id', 'desc')->first();
    if (!$booking) {
        echo "No bookings found in database.\n";
    } else {
        echo "Last Booking in Database ID: {$booking->id}\n";
        echo "Name: {$booking->name}\n";
        echo "Attempting to delete last booking (real deletion attempt)...\n";
        
        // We will do this and catch any error
        try {
            $booking->delete();
            echo "SUCCESS: Last booking deleted successfully!\n";
        } catch (\Throwable $ex) {
            echo "ERROR deleting last booking: " . $ex->getMessage() . "\n";
            echo $ex->getTraceAsString() . "\n";
        }
    }
    
    echo "\nAll steps completed!\n";
    
} catch (\Throwable $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
