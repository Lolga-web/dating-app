<?php

namespace App\Http\Resources\Invitations;

use App\Http\Resources\User\IndexResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserInvitationResource
 * @package App\Http\Resources\Invitations
 * @mixin Invitation
 *
 * @OA\Schema(schema="UserInvitationResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="invitation", ref="#/components/schemas/InvitationResource"),
 *     @OA\Property(property="from_user", ref="#/components/schemas/UserIndexResource"),
 *     @OA\Property(property="to_user", ref="#/components/schemas/UserIndexResource"),
 *     @OA\Property(property="answer", ref="#/components/schemas/InvitationAnswerResource"),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 * )
 */
class UserInvitationResource extends JsonResource
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
            'id' => $this->getKey(),
            'invitation' => InvitationResource::make($this->invitation),
            'from_user' => IndexResource::make($this->fromUser),
            'to_user' => IndexResource::make($this->toUser),
            'answer' => InvitationResource::make($this->answer),
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
        ];
    }
}
