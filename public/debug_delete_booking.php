<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "Debugging Booking Deletion:\n\n";

try {
    // 1. Check database connection
    DB::connection()->getPdo();
    echo "Database connection successful.\n";
    
    // 2. Count bookings
    $count = Booking::count();
    echo "Total Bookings: $count\n";
    
    // 3. Find a test booking or the last booking
    $booking = Booking::orderBy('id', 'desc')->first();
    if (!$booking) {
        echo "No bookings found in the database.\n";
        exit;
    }
    
    echo "Last Booking ID: {$booking->id}\n";
    echo "Name: {$booking->name}\n";
    echo "Email: {$booking->email}\n";
    echo "Created At: {$booking->created_at}\n\n";
    
    // 4. Check related records in other tables
    echo "Checking related records:\n";
    try {
        $reviewsCount = Review::where('booking_id', $booking->id)->count();
        echo "- Related reviews: $reviewsCount\n";
    } catch (\Throwable $ex) {
        echo "- Could not check reviews: " . $ex->getMessage() . "\n";
    }
    
    // Check foreign keys or constraints dynamically if possible, or try in a transaction
    echo "\nSimulating deletion in a transaction...\n";
    echo "STEP 1: Before beginTransaction\n";
    DB::beginTransaction();
    echo "STEP 2: After beginTransaction\n";
    try {
        echo "STEP 3: Before delete()\n";
        $booking->delete();
        echo "STEP 4: After delete()\n";
    } catch (\Throwable $ex) {
        echo "STEP 5: Catch block: " . $ex->getMessage() . "\n";
        echo $ex->getTraceAsString() . "\n";
    }
    echo "STEP 6: Before rollBack\n";
    DB::rollBack();
    echo "STEP 7: After rollBack\n";
    
} catch (\Throwable $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
