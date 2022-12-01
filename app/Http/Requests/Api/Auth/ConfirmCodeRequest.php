<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;

use App\Models\Users\UserEmailVerify;
use App\Models\Users\UserPhoneVerify;

use Illuminate\Validation\Rule;

/**
 * Class ConfirmCodeRequest
 * @package App\Http\Requests\Api\Auth
 *
 * @OA\Schema(schema="EmailConfirmCodeRequest", required={"email", "code"},
 *     @OA\Property(property="email", type="string", description="if email confirmation", example="some_user@gmail.com"),
 *     @OA\Property(property="code", type="string", example="111111"),
 * ),
 * @OA\Schema(schema="PhoneConfirmCodeRequest", required={"phone", "code"},
 *     @OA\Property(property="phone", type="string", description="if phone confirmation", example="+79999999999"),
 *     @OA\Property(property="code", type="string", example="111111"),
 * )
 */
class ConfirmCodeRequest extends ApiBaseRequest
{
    public function rules()
    {
        return [
            'code' => ['required', 'string', 'size:6'],
            'email' => [Rule::requiredIf(
                            $this->routeIs('confirm-email-by-code') || $this->routeIs('update-email')
                            ), 'email:rfc,dns', 'exists:email_verifies,email', 'bail',
                            function ($attribute, $value, $fail) {
                                $verification = UserEmailVerify::where('email', $this->input('email'))->first();
                                $message = __("This email already confirmed");
                                if ($verification->isConfirmed()) {
                                    $fail($message);
                                    return;
                                }
                                $message = __("Wrong code");
                                if (!$verification->checkCode($this->input('code'))) {
                                    $fail($message);
                                    return;
                                }
                            }
            ],
            'phone' => [Rule::requiredIf(
                            $this->routeIs('confirm-phone-by-code') || $this->routeIs('update-phone')
                            ), 'exists:phone_verifies,phone', Rule::phone()->detect(), 'bail',
                            function ($attribute, $value, $fail) {
                                $verification = UserPhoneVerify::where('phone', $this->input('phone'))->first();
                                $message = __("This phone already confirmed");
                                if ($verification->isConfirmed()) {
                                    $fail($message);
                                    return;
                                }
                                $message = __("Wrong code");
                                if (!$verification->checkCode($this->input('code'))) {
                                    $fail($message);
                                    return;
                                }
                            }
            ],
        ];
    }
}
