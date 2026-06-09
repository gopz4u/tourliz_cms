<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

try {
    $admin = Admin::where('role', 'super_admin')->first() ?: Admin::first();
    if (!$admin) {
        echo "No admin user found in database.";
        exit;
    }
    
    Auth::login($admin);
    header("Location: /admin/bookings");
    exit;
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
}
