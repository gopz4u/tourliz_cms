<?php
$logFile = dirname(__DIR__) . '/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    echo "Log file does not exist at: " . $logFile;
    exit;
}

$lines = 100;
$data = file($logFile);
$lineCount = count($data);
$start = max(0, $lineCount - $lines);

echo "<h3>Last $lines lines of laravel.log</h3>";
echo "<pre>";
for ($i = $start; $i < $lineCount; $i++) {
    echo htmlspecialchars($data[$i]);
}
echo "</pre>";
