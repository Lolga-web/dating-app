<?php

namespace App\Http\Resources\Invitations;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class InvitationResource
 * @package App\Http\Resources\Invitations
 * @mixin Invitation
 *
 * @OA\Schema(schema="InvitationResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="The Cinema"),
 * )
 *
 * @OA\Schema(schema="InvitationAnswerResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Yes, I agree"),
 * )
 */
class InvitationResource extends JsonResource
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
            'name' => $this->translate($request->header('X-Localization')) ? $this->translate($request->header('X-Localization'))->name : $this->name,
        ];
    }
}
