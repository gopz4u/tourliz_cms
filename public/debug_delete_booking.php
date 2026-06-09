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
    DB::beginTransaction();
    try {
        $booking->delete();
        echo "SUCCESS: Booking can be deleted successfully inside transaction (no DB constraint blocking).\n";
    } catch (\Throwable $ex) {
        echo "ERROR during deletion: " . $ex->getMessage() . "\n";
        echo $ex->getTraceAsString() . "\n";
    }
    DB::rollBack();
    echo "Transaction rolled back. No changes saved.\n";
    
} catch (\Throwable $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
