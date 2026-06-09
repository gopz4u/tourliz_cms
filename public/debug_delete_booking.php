<?php
$outputFile = __DIR__ . '/debug_output.txt';
file_put_contents($outputFile, "Start of script\n");

function logMsg($msg) {
    global $outputFile;
    file_put_contents($outputFile, $msg . "\n", FILE_APPEND);
    echo $msg . "\n";
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

logMsg("Boots Laravel successfully.");

try {
    $rawBooking = DB::selectOne("SELECT * FROM bookings ORDER BY id DESC LIMIT 1");
    if (!$rawBooking) {
        logMsg("No bookings found in database.");
        exit;
    }
    
    logMsg("Raw Booking ID: " . $rawBooking->id);
    
    logMsg("Loading with Eloquent...");
    $booking = Booking::find($rawBooking->id);
    if (!$booking) {
        logMsg("Failed to load booking with Eloquent.");
        exit;
    }
    logMsg("Loaded Eloquent model successfully.");
    
    logMsg("Attempting delete...");
    $booking->delete();
    logMsg("SUCCESS: Booking deleted!");

} catch (\Throwable $e) {
    logMsg("Error: " . $e->getMessage());
    logMsg($e->getTraceAsString());
}
