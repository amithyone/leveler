<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword;
use App\Notifications\TraineePasswordResetNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the trainee record associated with this user
     */
    public function trainee()
    {
        return $this->hasOne(Trainee::class);
    }

    /**
     * Check if user is a trainee
     */
    public function isTrainee(): bool
    {
        return $this->trainee !== null;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        \Log::info('User::sendPasswordResetNotification called', [
            'user_id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'token_length' => strlen($token),
            'token_preview' => substr($token, 0, 10) . '...',
        ]);

        try {
            $this->notify(new TraineePasswordResetNotification($token));
            \Log::info('Password reset notification sent successfully', [
                'user_id' => $this->id,
                'email' => $this->email,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset notification', [
                'user_id' => $this->id,
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
