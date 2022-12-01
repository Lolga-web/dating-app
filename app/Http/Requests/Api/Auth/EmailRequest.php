<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class EmailRequest
 * @package App\Http\Requests\Api\Auth
 *
 * @OA\Schema(schema="EmailRequest", required={"email"},
 *     @OA\Property(property="email", type="string", description="Input email", example="some_user@gmail.com"),
 * )
 */
class EmailRequest extends ApiBaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'nullable', 'email:rfc,dns', 'max:255', 'unique:users'],
        ];
    }
}
