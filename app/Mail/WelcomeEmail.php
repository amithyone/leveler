<?php

namespace App\Mail;

use App\Models\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainee;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(Trainee $trainee, $password = null)
    {
        $this->trainee = $trainee;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to Leveler - Your Registration is Complete')
                    ->view('emails.welcome')
                    ->with([
                        'trainee' => $this->trainee,
                        'password' => $this->password,
                        'user' => $this->trainee->user,
                    ]);
    }
}
