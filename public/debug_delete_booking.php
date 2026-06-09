<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

echo "Debugging New Last Booking:\n\n";

try {
    // 1. Get last booking using raw query first
    $rawBooking = DB::selectOne("SELECT * FROM bookings ORDER BY id DESC LIMIT 1");
    if (!$rawBooking) {
        echo "No bookings found in database.\n";
        exit;
    }
    
    echo "Raw Booking Data:\n";
    print_r($rawBooking);
    echo "\n";
    
    // 2. Load using Eloquent
    echo "Loading with Eloquent...\n";
    $booking = Booking::find($rawBooking->id);
    if (!$booking) {
        echo "Failed to load booking with Eloquent.\n";
        exit;
    }
    echo "Loaded Eloquent model successfully for ID: " . $booking->id . "\n";
    
    // 3. Try to delete the Eloquent model
    echo "Attempting Eloquent delete...\n";
    $booking->delete();
    echo "SUCCESS: Eloquent delete completed for ID: " . $rawBooking->id . "\n";

} catch (\Throwable $e) {
    echo "Error caught: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
