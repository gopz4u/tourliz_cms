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
    
    return null;
}

$startDir = dirname(__FILE__);
$projectRoot = findProjectRoot($startDir);

if (!$projectRoot) {
    echo "<p style='color: red;'><strong>Error:</strong> Could not find project root containing '.git' or 'artisan'.</p>";
    exit;
}

echo "<strong>Project Root Found:</strong> $projectRoot<br><br>";
chdir($projectRoot);

// Diagnostic info for .git permissions
echo "<strong>--- .git Directory Permissions Diagnostic ---</strong><br>";
if (file_exists('.git')) {
    echo ".git directory permissions: " . substr(sprintf('%o', fileperms('.git')), -4) . "<br>";
    echo ".git directory owner ID: " . fileowner('.git') . "<br>";
    if (file_exists('.git/FETCH_HEAD')) {
        echo ".git/FETCH_HEAD permissions: " . substr(sprintf('%o', fileperms('.git/FETCH_HEAD')), -4) . "<br>";
        echo ".git/FETCH_HEAD owner ID: " . fileowner('.git/FETCH_HEAD') . "<br>";
    } else {
        echo ".git/FETCH_HEAD does not exist.<br>";
    }
    // List some directories
    echo "Current PHP script executing user ID: " . getmyuid() . " / Name: " . get_current_user() . "<br>";
} else {
    echo "Warning: .git directory does not exist in " . getcwd() . "<br>";
}
echo "----------------------------------------------<br><br>";

echo "<strong>1. Running Git Fetch & Checkout...</strong><br>";

// Backup .htaccess files before git checkout/reset
$backups = [];
$startDirHtaccess = $startDir . '/.htaccess';
$projectRootHtaccess = $projectRoot . '/.htaccess';
$projectRootPublicHtaccess = $projectRoot . '/public/.htaccess';

if (file_exists($startDirHtaccess)) {
    $backups['startDirHtaccess'] = [
        'path' => $startDirHtaccess,
        'content' => file_get_contents($startDirHtaccess)
    ];
}
if (file_exists($projectRootHtaccess) && $projectRootHtaccess !== $startDirHtaccess) {
    $backups['projectRootHtaccess'] = [
        'path' => $projectRootHtaccess,
        'content' => file_get_contents($projectRootHtaccess)
    ];
}
if (file_exists($projectRootPublicHtaccess) && $projectRootPublicHtaccess !== $startDirHtaccess && $projectRootPublicHtaccess !== $projectRootHtaccess) {
    $backups['projectRootPublicHtaccess'] = [
        'path' => $projectRootPublicHtaccess,
        'content' => file_get_contents($projectRootPublicHtaccess)
    ];
}

$gitOutput = shell_exec('git fetch origin main && git checkout -f main && git reset --hard origin/main 2>&1');
echo "<pre>$gitOutput</pre>";

// Restore .htaccess files after git checkout/reset
foreach ($backups as $name => $backup) {
    file_put_contents($backup['path'], $backup['content']);
    echo "Restored $name to {$backup['path']}<br>";
}

// Auto-heal missing root .htaccess on Hostinger
if (!file_exists($projectRootHtaccess)) {
    $rootHtaccessContent = "<IfModule mod_rewrite.c>\n" .
                           "    RewriteEngine On\n" .
                           "    RewriteRule ^\$ public/ [L]\n" .
                           "    RewriteRule ((?s).*) public/\$1 [L]\n" .
                           "</IfModule>\n";
    file_put_contents($projectRootHtaccess, $rootHtaccessContent);
    echo "Auto-healed: Recreated missing root .htaccess at $projectRootHtaccess<br>";
}

echo "<strong>1a. Running Composer Install...</strong><br>";
$composerOutput = shell_exec('composer install --no-dev --optimize-autoloader 2>&1');
echo "<pre>$composerOutput</pre>";

echo "<strong>1b. Checking Git Status and Log...</strong><br>";
$gitStatus = shell_exec('git status 2>&1');
echo "<strong>Git Status:</strong><pre>$gitStatus</pre>";
$gitLog = shell_exec('git log -n 5 --oneline 2>&1');
echo "<strong>Recent Commits on Server:</strong><pre>$gitLog</pre>";

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

echo "<h3>Deployment completed!</h3>";

echo "<strong>Diagnostic: .htaccess files content</strong><br>";
if (file_exists($startDirHtaccess)) {
    echo "<strong>$startDirHtaccess Content:</strong><pre>" . htmlspecialchars(file_get_contents($startDirHtaccess)) . "</pre>";
} else {
    echo "No .htaccess in $startDirHtaccess<br>";
}
if (file_exists($projectRootHtaccess) && $projectRootHtaccess !== $startDirHtaccess) {
    echo "<strong>$projectRootHtaccess Content:</strong><pre>" . htmlspecialchars(file_get_contents($projectRootHtaccess)) . "</pre>";
}
if (file_exists($projectRootPublicHtaccess) && $projectRootPublicHtaccess !== $startDirHtaccess && $projectRootPublicHtaccess !== $projectRootHtaccess) {
    echo "<strong>$projectRootPublicHtaccess Content:</strong><pre>" . htmlspecialchars(file_get_contents($projectRootPublicHtaccess)) . "</pre>";
}

echo "<p style='color: red;'><strong>Important:</strong> Delete this file (deploy.php) from your public_html folder immediately for security.</p>";
