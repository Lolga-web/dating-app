<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Support\Facades\Hash;

/**
 * Class ResetPasswordRequest
 * @package App\Http\Requests\Api
 *
 * @OA\Schema(schema="ResetPasswordRequest", required={"old_password", "password", "password_confirmation"},
 *     @OA\Property(property="old_password", type="string"),
 *     @OA\Property(property="password", type="string"),
 *     @OA\Property(property="password_confirmation", type="string"),
 * )
 */
class ResetPasswordRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string', 'max:191', 'bail',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, \Auth::user()->password)) {
                        $fail(__("Old password is wrong"));
                        return;
                    }
                }
            ],
            'password' => ['required', 'string', 'max:191', 'min:6', 'confirmed'],
        ];
    }
}
