<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Trainee;
use App\Http\Controllers\Trainee\TraineeRegisterController;

class TraineeHelper
{
    /**
     * Get or create trainee record for a user
     * This is called when user enrolls in a course
     */
    public static function getOrCreateTrainee(User $user, array $registrationData = null)
    {
        // Check if user already has a trainee record
        if ($user->trainee) {
            return $user->trainee;
        }

        // Get registration data from session or use provided data
        if (!$registrationData) {
            $registrationData = session('registration_data', []);
        }

        // If no registration data, try to extract from user name
        if (empty($registrationData)) {
            $nameParts = explode(' ', $user->name, 3);
            $registrationData = [
                'surname' => $nameParts[0] ?? '',
                'first_name' => $nameParts[1] ?? '',
                'middle_name' => $nameParts[2] ?? null,
                'gender' => 'M', // Default
                'phone_number' => '', // Will need to be collected
            ];
        }

        // Generate username
        $username = TraineeRegisterController::generateUsername();

        // Create trainee record
        $trainee = Trainee::create([
            'user_id' => $user->id,
            'surname' => $registrationData['surname'] ?? '',
            'first_name' => $registrationData['first_name'] ?? '',
            'middle_name' => $registrationData['middle_name'] ?? null,
            'gender' => $registrationData['gender'] ?? 'M',
            'phone_number' => $registrationData['phone_number'] ?? '',
            'username' => $username,
            'password' => $user->password, // Use user's password
            'status' => 'Inactive', // Will be activated when payment is completed
        ]);

        return $trainee;
    }

    /**
     * Get trainee for current authenticated user
     */
    public static function getCurrentTrainee()
    {
        $user = auth()->user();
        
        if (!$user) {
            return null;
        }

        return $user->trainee;
    }
}

