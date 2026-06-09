<?php
$files = [
    __DIR__ . '/debug_output.txt',
    __DIR__ . '/debug_delete_booking.php',
    __DIR__ . '/view_logs.php',
    __DIR__ . '/auto_login.php',
];

header('Content-Type: text/plain');
echo "Starting cleanup of debug files:\n\n";

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
echo "Cleanup script deleted itself.\n";
