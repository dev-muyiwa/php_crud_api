<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOtpNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected int $otp
    )
    {
    }

    public function build(): NewOtpNotification
    {
        return $this->subject("Verify your Account")
            ->html("<p>Your One Time Pin is {$this->otp}. Expires in 5 minutes.</p>");
    }
}
