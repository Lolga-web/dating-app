<?php

namespace App\Mail\Auth;

use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @var User $user*/
    private User $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->subject(__('Your password changed'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ResetSuccessMail
    {
        return $this->view('mail.auth.reset-success');
    }
}
