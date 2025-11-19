<?php

namespace App\Console\Commands;

use App\Models\Trainee;
use App\Models\Payment;
use Illuminate\Console\Command;

class CreateSampleTrainee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainee:create-sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a sample trainee user with payment for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if sample trainee already exists
        $existingTrainee = Trainee::where('username', 'SAMPLE01')->first();
        
        if ($existingTrainee) {
            $this->warn('Sample trainee already exists!');
            $this->info("Username: SAMPLE01");
            $this->info("Password: SAMPLE");
            return 0;
        }

        // Create sample trainee
        $trainee = Trainee::create([
            'surname' => 'SAMPLE',
            'first_name' => 'TRAINEE',
            'middle_name' => 'TEST',
            'gender' => 'M',
            'username' => 'SAMPLE01',
            'password' => 'SAMPLE',
            'phone_number' => '2348012345678',
            'status' => 'Active',
        ]);

        // Create completed payment for the trainee
        Payment::create([
            'trainee_id' => $trainee->id,
            'amount' => 50000.00,
            'payment_method' => 'Bank Transfer',
            'payment_date' => now(),
            'status' => 'Completed',
            'receipt_number' => 'REC-' . str_pad($trainee->id, 6, '0', STR_PAD_LEFT),
            'notes' => 'Sample payment for testing purposes',
        ]);

        $this->info('Sample trainee created successfully!');
        $this->newLine();
        
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $trainee->full_name],
                ['Username', 'SAMPLE01'],
                ['Password', 'SAMPLE'],
                ['Status', 'Active'],
                ['Payment', 'Completed (₦50,000.00)'],
            ]
        );

        $this->newLine();
        $this->info('You can now login at: /trainee/login');
        $this->info('Or use "View As" from admin panel to see their view.');

        return 0;
    }
}
