<?php
// Set execution time limit to 5 minutes
set_time_limit(300);

echo "<h2>Tourliz CMS Auto-Deployment Script</h2>";

// Find the directory containing the '.git' folder or 'artisan' file
function findProjectRoot($startDir) {
    $current = realpath($startDir);
    // Check up to 4 levels up
    for ($i = 0; $i < 4; $i++) {
        if (file_exists($current . '/artisan') && (file_exists($current . '/.git') || file_exists($current . '/composer.json'))) {
            return $current;
        }
        $parent = dirname($current);
        if ($parent === $current) break;
        $current = $parent;
    }
    
    // Check subdirectories of the parent of startDir (one level up from public_html)
    $parentOfStart = dirname($startDir);
    if (file_exists($parentOfStart)) {
        $files = scandir($parentOfStart);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $path = $parentOfStart . '/' . $file;
            if (is_dir($path)) {
                if (file_exists($path . '/artisan')) {
                    return realpath($path);
                }
            }
        }
    }
    
    // Check subdirectories of startDir itself (public_html/tourliz_cms, etc.)
    $files = scandir($startDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $startDir . '/' . $file;
        if (is_dir($path)) {
            if (file_exists($path . '/artisan')) {
                return realpath($path);
            }
        }
    }
    
    return null;
}

$startDir = dirname(__FILE__); // directory of deploy.php (usually public_html)
$projectRoot = findProjectRoot($startDir);

if (!$projectRoot) {
    echo "<p style='color: red;'><strong>Error:</strong> Could not find project root containing '.git' or 'artisan'.</p>";
    echo "Current directory: " . $startDir . "<br>";
    echo "Directories list in " . dirname($startDir) . ":<pre>";
    print_r(scandir(dirname($startDir)));
    echo "</pre>";
    echo "Directories list in " . $startDir . ":<pre>";
    print_r(scandir($startDir));
    echo "</pre>";
    exit;
}

echo "<strong>Project Root Found:</strong> $projectRoot<br><br>";
chdir($projectRoot);

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
