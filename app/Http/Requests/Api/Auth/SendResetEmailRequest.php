<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class SendResetEmailRequest
 * @package App\Http\Requests\Api\Auth
 *
 * @OA\Schema(schema="SendResetEmailRequest", required={"email"},
 *     @OA\Property(property="email", type="string", description="Input email", example="some_user@gmail.com"),
 * )
 */
class SendResetEmailRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'exists:users,email'],
        ];
    }
}
