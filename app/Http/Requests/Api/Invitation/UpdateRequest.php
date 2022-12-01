<?php

namespace App\Http\Requests\Api\Invitation;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Api\Invitation
 *
 *  @OA\Schema(schema="InvitationUpdateRequest", required={"answer_id"},
 *     @OA\Property(property="answer_id", type="integer", example=1),
 * )
 */
class UpdateRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'answer_id' => ['required', 'numeric', 'exists:invitation_answers,id'],
            'invitation' => ['required', 'bail',
                                function ($attribute, $value, $fail) {
                                    if ($value->toUser->isNot($this->user())) {
                                        $fail(__("You can not reply to this invitation"));
                                        return;
                                    }
                                    if ($value->status !== null) {
                                        $fail(__("Already answered"));
                                        return;
                                    }
                                }
                            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge(['invitation' => $this->route('invitation')]);
    }
}
