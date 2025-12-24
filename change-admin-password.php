<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'admin@leveler.com';
$newPassword = 'Enter0text';

$user = User::where('email', $email)->first();

if (!$user) {
    echo "User with email '{$email}' not found!\n";
    exit(1);
}

$user->password = Hash::make($newPassword);
$user->save();

echo "Password successfully changed for {$email}\n";
echo "New password: {$newPassword}\n";

