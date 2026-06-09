<?php
header('Content-Type: text/plain');

$file = __DIR__ . '/../app/Http/Controllers/Admin/DashboardController.php';
if (file_exists($file)) {
    echo "=== DashboardController.php on Live Server ===\n";
    echo file_get_contents($file);
} else {
    echo "DashboardController.php not found at: $file\n";
}
