<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class SendPasswordResetToAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:send-password-reset {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send password reset emails to all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No emails will be sent');
        }
        
        $users = User::whereNotNull('email')->get();
        
        if ($users->isEmpty()) {
            $this->error('No users found in the database.');
            return Command::FAILURE;
        }
        
        $this->info("Found {$users->count()} user(s) to process.");
        
        if (!$dryRun && !$this->confirm('Do you want to send password reset emails to all users?', true)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }
        
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();
        
        $successCount = 0;
        $failureCount = 0;
        $errors = [];
        
        foreach ($users as $user) {
            try {
                if (!$dryRun) {
                    $status = Password::broker('users')->sendResetLink(['email' => $user->email]);
                    
                    if ($status === Password::RESET_LINK_SENT) {
                        $successCount++;
                        Log::info("Password reset email sent to: {$user->email}");
                    } else {
                        $failureCount++;
                        $errors[] = "{$user->email}: {$status}";
                        Log::warning("Failed to send password reset to {$user->email}: {$status}");
                    }
                } else {
                    $successCount++;
                    $this->line("\nWould send password reset to: {$user->email} ({$user->name})");
                }
            } catch (\Exception $e) {
                $failureCount++;
                $errors[] = "{$user->email}: {$e->getMessage()}";
                Log::error("Error sending password reset to {$user->email}: {$e->getMessage()}");
            }
            
            $bar->advance();
            
            // Small delay to avoid overwhelming the mail server
            if (!$dryRun) {
                usleep(100000); // 0.1 second delay
            }
        }
        
        $bar->finish();
        $this->newLine(2);
        
        // Summary
        $this->info("✅ Successfully processed: {$successCount}");
        if ($failureCount > 0) {
            $this->error("❌ Failed: {$failureCount}");
            if (!empty($errors)) {
                $this->warn("\nErrors:");
                foreach ($errors as $error) {
                    $this->line("  - {$error}");
                }
            }
        }
        
        if ($dryRun) {
            $this->info("\n💡 Run without --dry-run to actually send the emails.");
        } else {
            $this->info("\n✅ Password reset emails sent successfully!");
        }
        
        return Command::SUCCESS;
    }
}
