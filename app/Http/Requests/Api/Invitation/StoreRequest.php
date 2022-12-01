<?php

namespace App\Http\Requests\Api\Invitation;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class StoreRequest
 * @package App\Http\Requests\Api\Invitation
 *
 *  @OA\Schema(schema="InvitationStoreRequest", required={"invitation_id", "user_id"},
 *     @OA\Property(property="invitation_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 * )
 */
class StoreRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'invitation_id' => ['required', 'numeric', 'exists:invitations,id'],
            'user_id' => ['required', 'numeric', 'exists:users,id'],
        ];
    }
}
