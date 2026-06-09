<pre>
<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "Columns of bookings table:\n";
    $columns = Schema::getColumnListing('bookings');
    print_r($columns);
} catch (\Exception $e) {
    echo "Error listing columns: " . $e->getMessage() . "\n";
}
?>
</pre>
