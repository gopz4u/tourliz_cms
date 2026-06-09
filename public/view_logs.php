<?php
$logFile = dirname(__DIR__) . '/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    echo "Log file does not exist at: " . $logFile;
    exit;
}

$bytesToRead = 100000; // Read last 100KB of logs
$size = filesize($logFile);
$file = fopen($logFile, 'r');

if ($file) {
    if ($size > $bytesToRead) {
        fseek($file, -$bytesToRead, SEEK_END);
    }
    
    // Discard the first partial line if we seeked
    if ($size > $bytesToRead) {
        fgets($file);
    }

    echo "<h3>Last " . round($bytesToRead / 1024) . " KB of laravel.log</h3>";
    echo "<pre>";
    while (!feof($file)) {
        echo htmlspecialchars(fgets($file));
    }
    echo "</pre>";
    fclose($file);
} else {
    echo "Failed to open log file.";
}
