<?php

namespace App\Builders;

use App\Models\Chat;
use App\Models\Users\User;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ChatBuilder
 * @package App\Builders
 * @see https://timacdonald.me/dedicated-eloquent-model-query-builders/
 */
class ChatBuilder extends Builder
{
    /**
     * @param User $user
     * @return $this
     */
    public function chat(User $user): self
    {
        $this->where([['recipient_id', \Auth::id()], ['sender_id', $user->id]])
             ->orWhere([['sender_id', \Auth::id()], ['recipient_id', $user->id]]);

        return $this;
    }

    /**
     * @return Chat
     */
    public function lastMessage(): Chat
    {
        return $this->latest()->firstOrFail();
    }

    /**
    * @return array
    */
    public function getChatsUsers(): array
    {
        return Chat::where('recipient_id', \Auth::id())
                    ->where(function ($query) {
                        $query->whereNotIn('deleted_by', [\Auth::id()])
                              ->orWhere('deleted_by', null);
                    })
                    ->pluck('sender_id')
                    ->merge(Chat::where('sender_id', \Auth::id())
                                ->where(function ($query) {
                                    $query->whereNotIn('deleted_by', [\Auth::id()])
                                          ->orWhere('deleted_by', null);
                                })
                                ->pluck('recipient_id'))
                    ->unique()
                    ->values()
                    ->toArray();
    }
}
