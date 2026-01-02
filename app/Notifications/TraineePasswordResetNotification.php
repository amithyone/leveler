<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class TraineePasswordResetNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
        Log::info('TraineePasswordResetNotification created', [
            'token_length' => strlen($token),
            'token_preview' => substr($token, 0, 10) . '...',
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        Log::info('TraineePasswordResetNotification::via called', [
            'notifiable_type' => get_class($notifiable),
            'notifiable_email' => $notifiable->email ?? 'N/A',
            'notifiable_id' => $notifiable->id ?? 'N/A',
        ]);
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::info('TraineePasswordResetNotification::toMail called', [
            'notifiable_email' => $notifiable->email ?? 'N/A',
            'notifiable_id' => $notifiable->id ?? 'N/A',
            'notifiable_name' => $notifiable->name ?? 'N/A',
        ]);

        $url = url(route('trainee.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email
        ], false));

        Log::info('Password reset URL generated', [
            'email' => $notifiable->email ?? 'N/A',
            'url' => $url,
            'route_name' => 'trainee.password.reset',
        ]);

        try {
            // Use custom view with logo signature
            $mailMessage = (new MailMessage)
                ->subject('Reset Your Password - Leveler Training Platform')
                ->view('emails.password-reset', [
                    'name' => $notifiable->name ?? 'Trainee',
                    'url' => $url,
                ]);

            Log::info('MailMessage created successfully', [
                'email' => $notifiable->email ?? 'N/A',
            ]);

            return $mailMessage;
        } catch (\Exception $e) {
            Log::error('Error creating MailMessage', [
                'email' => $notifiable->email ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
