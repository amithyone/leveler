<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Handle a user registration request.
     * Creates a User account. They become a Trainee when they enroll in a course.
     */
    public function register(Request $request)
    {
        $request->validate([
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'required|in:M,F',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create user account
        $fullName = strtoupper($request->surname . ' ' . $request->first_name . ($request->middle_name ? ' ' . $request->middle_name : ''));
        
        $user = User::create([
            'name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Regular user, not admin
        ]);

        // Store additional info in session for when they become a trainee
        $request->session()->put('registration_data', [
            'surname' => strtoupper($request->surname),
            'first_name' => strtoupper($request->first_name),
            'middle_name' => $request->middle_name ? strtoupper($request->middle_name) : null,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
        ]);

        // Automatically log in the user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('trainee.dashboard')
            ->with('success', 'Registration successful! Select a course and make a payment to become a trainee and gain access to courses.');
    }

    /**
     * Generate a unique username (format: BCD/XXXXXX)
     * Used when creating trainee record from user
     */
    public static function generateUsername()
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

