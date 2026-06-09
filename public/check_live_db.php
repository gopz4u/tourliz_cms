<?php
header('Content-Type: text/plain');

$file = __DIR__ . '/../app/Http/Controllers/Admin/DashboardController.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    echo "Filesize: " . strlen($content) . " bytes\n";
    echo "MD5 hash: " . md5($content) . "\n";
    echo "Line count: " . count(explode("\n", $content)) . "\n";
    
    // Output first 50 lines
    echo "=== First 50 ===\n";
    $lines = explode("\n", $content);
    echo implode("\n", array_slice($lines, 0, 50)) . "\n";
    
    // Output next 50 lines
    echo "=== Next 50 ===\n";
    echo implode("\n", array_slice($lines, 50, 50)) . "\n";
    
    // Output remaining lines
    echo "=== Remaining ===\n";
    echo implode("\n", array_slice($lines, 100)) . "\n";
} else {
    echo "Not found\n";
}
