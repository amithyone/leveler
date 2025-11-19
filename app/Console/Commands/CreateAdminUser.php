<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin 
                            {--name=Admin User : The name of the admin user}
                            {--email=admin@leveler.com : The email of the admin user}
                            {--password=password : The password for the admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');

        // Validate email
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email address!');
            return 1;
        }

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->warn("User with email {$email} already exists. Updating...");
            $user = User::where('email', $email)->first();
            $user->update([
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin',
            ]);
            $this->info("Admin user updated successfully!");
        } else {
            // Create new admin user
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->info("Admin user created successfully!");
        }

        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $name],
                ['Email', $email],
                ['Password', $password],
                ['Role', 'admin'],
            ]
        );

        $this->warn('Please change the default password after first login!');

        return 0;
    }
}
