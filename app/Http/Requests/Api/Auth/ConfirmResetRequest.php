<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;

use App\Models\PasswordResets;

use Illuminate\Validation\Rule;

/**
 * Class ConfirmResetRequest
 * @package App\Http\Requests\Api\Auth
 *
 * @OA\Schema(schema="ConfirmResetRequest", required={"email", "code", "password", "password_confirmation"},
 *     @OA\Property(property="email", type="string", description="User email", example="some_user@gmail.com"),
 *     @OA\Property(property="code", type="string", example="111111"),
 *     @OA\Property(property="password", type="string"),
 *     @OA\Property(property="password_confirmation", type="string"),
 * )
 */
class ConfirmResetRequest extends ApiBaseRequest
{
    public function rules()
    {
        return [
            'code' => ['required', 'string', 'size:6'],
            'email' => [Rule::requiredIf($this->routeIs('reset-by-email-code')),
                        'email:rfc,dns', 'exists:users,email', 'exists:password_resets,email', 'bail',
                            function ($attribute, $value, $fail) {
                                $reset = PasswordResets::where('email', $this->input('email'))->first();
                                $message = __("Wrong code");
                                if (!$reset->checkCode($this->input('code'))) {
                                    $fail($message);
                                    return;
                                }
                            }
            ],
            'phone' => [Rule::requiredIf($this->routeIs('reset-by-sms-code')),
                        Rule::phone()->detect(), 'exists:users,phone', 'exists:password_resets,phone', 'bail',
                            function ($attribute, $value, $fail) {
                                $reset = PasswordResets::where('phone', $this->input('phone'))->first();
                                $message = __("Wrong code");
                                if (!$reset->checkCode($this->input('code'))) {
                                    $fail($message);
                                    return;
                                }
                            }
            ],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }
}
