<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;

use App\Models\Users\UserEmailVerify;
use App\Models\Users\UserPhoneVerify;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

/**
 * Class RegistrationRequest
 * @package App\Http\Requests\Api\Auth
 *
 * @OA\Schema(schema="RegistrationRequest", required={"email", "name", "password", "password_confirmation", "gender", "language", "birthday"},
 *     @OA\Property(property="email", type="string", description="User email", example="some_user@gmail.com"),
 *     @OA\Property(property="name", type="string", description="User name", example="Ivan Ivanov"),
 *     @OA\Property(property="password", type="string"),
 *     @OA\Property(property="password_confirmation", type="string"),
 *     @OA\Property(property="gender", type="string", description="User gender", example="male / female"),
 *     @OA\Property(property="language", type="string", description="Locale", example="en"),
 *     @OA\Property(property="birthday", type="date", example="2000-01-01", description="User birthday"),
 *     @OA\Property(property="look_for", type="array", @OA\Items(type="string"), example={"male","female"}),
 * )
 */
class RegistrationRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $this->sanitize();

        return [
            'email' => [
                Rule::requiredIf($this->routeIs('email-register')),
                'string', 'nullable', 'email:rfc,dns', 'max:191', 'unique:users,email', 'bail',
                function ($attribute, $value, $fail) {
                    if ($this->routeIs('email-register')) {
                        $verification = UserEmailVerify::where('email', $this->input('email'))->confirmed()->first();
                        $message = __("Email has not been verified");
                        if (!$verification) {
                            $fail($message);
                            return;
                        }
                    }
                }
            ],
            'phone' => [
                Rule::requiredIf($this->routeIs('phone-register')),
                'string', 'nullable', Rule::phone()->detect(), 'max:64', 'unique:users,phone', 'bail',
                function ($attribute, $value, $fail) {
                    if ($this->routeIs('phone-register')) {
                        $verification = UserPhoneVerify::where('phone', $this->input('phone'))->confirmed()->first();
                        $message = __("Phone has not been verified");
                        if (!$verification) {
                            $fail($message);
                            return;
                        }
                    }
                }
            ],
            'name' => ['required', 'string', 'min:1', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'gender' => ['required', 'string', 'in:male,female'],
            'language' => ['required', 'string', 'in:'. implode(',', config('translatable.locales'))],
            'birthday' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d')],

            'look_for' => ['required', 'array'],
            'look_for.*' => ['required_with:look_for', 'string', 'in:male,female'],
        ];
    }
}
