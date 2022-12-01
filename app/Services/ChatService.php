<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Chat;
use App\Models\Users\User;

class ChatService
{
    /**
     * @param User $user
     * @param int $authId
     */
    public function deleteChat(User $user)
    {
        $deleted = Chat::chat($user)
                        ->lastMessage()
                        ->isDeleted($user);

        if ($deleted) {
            Chat::chat($user)->delete();
        } else {
            Chat::chat($user)->update(['deleted_by' => \Auth::id()]);
        }
    }
}
