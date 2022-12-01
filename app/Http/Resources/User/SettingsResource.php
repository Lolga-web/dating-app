<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SettingsResource
 * @package App\Http\Resources\User
 * @mixin User
 *
 * @OA\Schema(schema="SettingsResource",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="block_messages", type="boolean"),
 *     @OA\Property(property="matches", type="boolean"),
 *     @OA\Property(property="invisible", type="boolean"),
 *     @OA\Property(property="language", type="string", example="en"),
 *     @OA\Property(property="notifications", type="object",
 *          @OA\Property(property="likes", type="boolean"),
 *          @OA\Property(property="matches", type="boolean"),
 *          @OA\Property(property="invitations", type="boolean"),
 *          @OA\Property(property="messages", type="boolean"),
 *          @OA\Property(property="guests", type="boolean"),
 *     ),
 * )
 */
class SettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user_id' => $this->user_id,
            'block_messages' => $this->block_messages,
            'matches' => $this->matches,
            'invisible' => $this->invisible,
            'language' => \Auth::user()->language,
            'notifications' => [
                'likes' => $this->likes_notifications,
                'matches' => $this->matches_notifications,
                'invitations' => $this->invitations_notifications,
                'messages' => $this->messages_notifications,
                'guests' => $this->guests_notifications,
            ],
        ];
    }
}
