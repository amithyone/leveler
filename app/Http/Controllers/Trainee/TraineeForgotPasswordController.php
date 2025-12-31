<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class TraineeForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showLinkRequestForm()
    {
        return view('trainee.auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        
        Log::info('=== PASSWORD RESET REQUEST START ===', [
            'email' => $email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Check if user exists
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            Log::warning('Password reset requested for non-existent email', [
                'email' => $email,
            ]);
        } else {
            Log::info('User found for password reset', [
                'email' => $email,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'has_trainee' => $user->trainee ? 'yes' : 'no',
            ]);
        }

        // Check mail configuration
        Log::info('Mail configuration check', [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ]);

        // Use the 'users' password broker (which uses User model)
        // The User model has sendPasswordResetNotification that uses TraineePasswordResetNotification
        try {
            Log::info('Calling Password::broker()->sendResetLink', [
                'email' => $email,
                'broker' => 'users',
            ]);

            $status = Password::broker('users')->sendResetLink(
                $request->only('email')
            );

            Log::info('Password reset link send attempt completed', [
                'email' => $email,
                'status' => $status,
                'status_code' => $status == Password::RESET_LINK_SENT ? 'RESET_LINK_SENT' : 'OTHER',
                'RESET_LINK_SENT' => Password::RESET_LINK_SENT,
                'INVALID_USER' => Password::INVALID_USER,
                'RESET_THROTTLED' => Password::RESET_THROTTLED,
            ]);

            if ($status == Password::RESET_LINK_SENT) {
                Log::info('=== PASSWORD RESET SUCCESS ===', [
                    'email' => $email,
                    'message' => 'Password reset link sent successfully',
                ]);
                return back()->with('status', __($status));
            } else {
                Log::warning('=== PASSWORD RESET FAILED ===', [
                    'email' => $email,
                    'status' => $status,
                    'status_message' => __($status),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('=== PASSWORD RESET EXCEPTION ===', [
                'email' => $email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}

