<?php
// Set execution time limit to 5 minutes
set_time_limit(300);

echo "<h2>Tourliz CMS Auto-Deployment Script</h2>";

// Move to the parent directory (project root)
$projectRoot = dirname(__DIR__);
chdir($projectRoot);

echo "Working directory: " . getcwd() . "<br><br>";

echo "<strong>1. Running Git Pull...</strong><br>";
$gitOutput = shell_exec('git pull origin main 2>&1');
echo "<pre>$gitOutput</pre>";

echo "<strong>2. Clearing Configuration Cache...</strong><br>";
$configClear = shell_exec('php artisan config:clear 2>&1');
echo "<pre>$configClear</pre>";

echo "<strong>3. Clearing Application Cache...</strong><br>";
$cacheClear = shell_exec('php artisan cache:clear 2>&1');
echo "<pre>$cacheClear</pre>";

echo "<strong>4. Clearing Route Cache...</strong><br>";
$routeClear = shell_exec('php artisan route:clear 2>&1');
echo "<pre>$routeClear</pre>";

echo "<strong>5. Clearing View Cache...</strong><br>";
$viewClear = shell_exec('php artisan view:clear 2>&1');
echo "<pre>$viewClear</pre>";

echo "<strong>6. Running Migrations (if any)...</strong><br>";
$migrationOutput = shell_exec('php artisan migrate --force 2>&1');
echo "<pre>$migrationOutput</pre>";

echo "<h3>Deployment completed successfully!</h3>";
echo "<p style='color: red;'><strong>Important:</strong> Delete this file (deploy.php) from your public_html folder immediately for security.</p>";
