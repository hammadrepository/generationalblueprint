<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpSend extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $otp)
    {
        $this->user = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subject = 'OTP - Generational BluePrint';
        return $this->view('email.template')
            ->with('data',$this->otp)
            ->from(config('mail.from.address'))
            ->subject($subject);
    }
}
