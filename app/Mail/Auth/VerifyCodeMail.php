<?php

namespace App\Mail\Auth;

use App\Models\Users\UserEmailVerify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyCodeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @var UserEmailVerify $userMobileEmailVerify */
    private UserEmailVerify $userEmailVerify;

    /**
     * Create a new message instance.
     *
     * @param UserMobileEmailVerify $userMobileEmailVerify
     */
    public function __construct(UserEmailVerify $userEmailVerify)
    {
        $this->userEmailVerify = $userEmailVerify;
        $this->subject(__('Email confirmation'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('mail.auth.verify-email-code', [
            'code' => $this->userEmailVerify->code,
        ]);
    }
}
