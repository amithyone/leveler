<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TraineeAuthController extends Controller
{
    /**
     * Show the trainee login form.
     */
    public function showLoginForm()
    {
        return view('trainee.auth.login');
    }

    /**
     * Handle a user login request.
     * Users log in with email/password, they become trainees when they enroll.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Try to authenticate as User (email/password)
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('trainee.dashboard'));
        }

        // Also check if they're trying to login with old trainee username
        // (for backward compatibility during migration)
        $trainee = Trainee::where('username', $request->email)->first();
        if ($trainee && $trainee->user) {
            // If trainee has a user, try to login with user credentials
            if (Auth::attempt([
                'email' => $trainee->user->email,
                'password' => $request->password
            ], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('trainee.dashboard'));
            }
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('trainee.login');
    }
}
