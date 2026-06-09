<?php
$files = [
    __DIR__ . '/check_bookings_table.php',
    __DIR__ . '/run_db_update.php',
];

header('Content-Type: text/plain');
echo "Starting cleanup of debug files phase 2:\n\n";

foreach ($files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "Successfully deleted: " . basename($file) . "\n";
        } else {
            echo "Failed to delete: " . basename($file) . "\n";
        }
    } else {
        echo "File not found: " . basename($file) . "\n";
    }
}

// Delete itself
@unlink(__FILE__);
echo "Cleanup script phase 2 deleted itself.\n";
