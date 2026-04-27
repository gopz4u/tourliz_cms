<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the Currency response
$rates = \App\Models\CurrencyExchangeRate::all();
echo json_encode($rates);
