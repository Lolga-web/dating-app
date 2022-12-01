<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     */
    public function __construct()
    {
        $this->subject(__('Welcome!'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): WelcomeMail
    {
        return $this->view('mail.auth.welcome');
    }
}
