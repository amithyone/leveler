<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
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
     * Handle a trainee login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $trainee = Trainee::where('username', $request->username)->first();

        if (!$trainee) {
            throw ValidationException::withMessages([
                'username' => __('The provided credentials do not match our records.'),
            ]);
        }

        // Check if trainee is active
        if ($trainee->status !== 'Active') {
            throw ValidationException::withMessages([
                'username' => __('Your account is not active. Please contact the administrator.'),
            ]);
        }

        // Check password (plain text comparison)
        if ($trainee->password !== $request->password) {
            throw ValidationException::withMessages([
                'username' => __('The provided credentials do not match our records.'),
            ]);
        }

        // Manually log in the trainee
        Auth::guard('trainee')->login($trainee, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('trainee.dashboard'));
    }

    /**
     * Log the trainee out.
     */
    public function logout(Request $request)
    {
        Auth::guard('trainee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('trainee.login');
    }
}
