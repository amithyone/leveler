<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangeAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:change-password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change password for an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found!");
            return 1;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password successfully changed for {$email}");

        return 0;
    }
}

