<?php

/**
 * Quick script to create an admin user
 * Run this after migrations: php create-admin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $admin = User::firstOrCreate(
        ['email' => 'admin@leveler.com'],
        [
            'name' => 'Admin User',
            'email' => 'admin@leveler.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]
    );

    echo "\n✅ Admin user created successfully!\n\n";
    echo "Email: admin@leveler.com\n";
    echo "Password: password\n";
    echo "Role: admin\n\n";
    echo "⚠️  Please change the password after first login!\n\n";
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Make sure MySQL is running and migrations are completed.\n\n";
}

