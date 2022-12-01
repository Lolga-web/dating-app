<?php

namespace App\Mail\Auth;

use App\Models\PasswordResets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordCodeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @var PasswordResets $reset */
    private PasswordResets $reset;

    /**
     * Create a new message instance.
     *
     * @param PasswordReset $reset
     */
    public function __construct(PasswordResets $reset)
    {
        $this->reset = $reset;
        $this->subject(__('Password reset request'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ResetPasswordCodeMail
    {
        return $this->view('mail.auth.reset-password-code')->with([
            'code' => $this->reset->code,
        ]);
    }
}
