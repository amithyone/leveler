<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TraineeRegisterController extends Controller
{
    /**
     * Show the trainee registration form.
     */
    public function showRegistrationForm()
    {
        return view('trainee.auth.register');
    }

    /**
     * Handle a trainee registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'required|in:M,F',
            'phone_number' => 'required|string|max:20|unique:trainees,phone_number',
            'username' => 'nullable|string|unique:trainees,username|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Generate username if not provided
        $username = $request->username;
        if (empty($username)) {
            $username = $this->generateUsername();
        }

        // Generate password if not provided (but we require it in registration)
        $password = $request->password;

        // Create trainee account (status will be Inactive until payment is made)
        $trainee = Trainee::create([
            'surname' => strtoupper($request->surname),
            'first_name' => strtoupper($request->first_name),
            'middle_name' => $request->middle_name ? strtoupper($request->middle_name) : null,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'username' => $username,
            'password' => $password, // Store as plain text (as per existing system)
            'status' => 'Inactive', // Inactive until payment is made
        ]);

        // Automatically log in the trainee
        Auth::guard('trainee')->login($trainee);
        $request->session()->regenerate();

        return redirect()->route('trainee.dashboard')
            ->with('success', 'Registration successful! Please make a payment to activate your account and gain access to courses.');
    }

    /**
     * Generate a unique username (format: BCD/XXXXXX)
     */
    private function generateUsername()
    {
        $lastTrainee = Trainee::orderBy('id', 'desc')->first();
        $nextNumber = $lastTrainee ? (int) substr($lastTrainee->username, 4) + 1 : 1;
        $username = 'BCD/' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (Trainee::where('username', $username)->exists()) {
            $nextNumber++;
            $username = 'BCD/' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        }

        return $username;
    }
}

