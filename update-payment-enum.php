<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('Cash', 'Bank Transfer', 'Mobile Money', 'Card', 'Other', 'Manual Payment') DEFAULT 'Cash'");
    echo "Payment method enum updated successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

