<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $trainee;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, Trainee $trainee)
    {
        $this->payment = $payment;
        $this->trainee = $trainee;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Payment Receipt - Leveler Training Platform')
                    ->view('emails.payment-receipt')
                    ->with([
                        'payment' => $this->payment,
                        'trainee' => $this->trainee,
                        'user' => $this->trainee->user,
                    ]);
    }
}
