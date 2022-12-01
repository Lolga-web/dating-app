<?php

namespace App\Http\Requests\Api\Admin\User;

use App\Http\Requests\Api\ApiBaseRequest;

use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

/**
 * Class StoreRequest
 * @package App\Http\Requests\Api\Auth
 *
 * @OA\Schema(schema="AdninUserStoreRequest", required={"email", "phone", "name", "password", "password_confirmation", "gender", "language", "birthday"},
 *     @OA\Property(property="email", type="string", description="User email", example="some_user@gmail.com"),
 *     @OA\Property(property="phone", type="string", description="User phone", example="+79999999999"),
 *     @OA\Property(property="name", type="string", description="User name", example="Ivan Ivanov"),
 *     @OA\Property(property="password", type="string"),
 *     @OA\Property(property="password_confirmation", type="string"),
 *     @OA\Property(property="gender", type="string", description="User gender", example="male / female"),
 *     @OA\Property(property="language", type="string", description="Locale", example="en"),
 *     @OA\Property(property="birthday", type="date", example="2000-01-01", description="User birthday"),
 *     @OA\Property(property="photo", type="file", description="Photo to upload"),
 *     @OA\Property(property="look_for[]", type="array", @OA\Items(type="string"), example={"male"}),
 * )
 */
class StoreRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        return [
            'name' => ['required', 'string', 'min:1', 'max:20'],
            'email' => ['required_without:phone', 'string', 'nullable', 'email:rfc,dns', 'max:191', 'unique:users,email'],
            'phone' => ['required_without:email', 'string', 'nullable', Rule::phone()->detect(), 'max:64', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'gender' => ['required', 'string', 'in:male,female'],
            'language' => ['required', 'string', 'in:'. implode(',', config('translatable.locales'))],
            'birthday' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d')],
            'photo' => ['file' => 'max:5000', 'file.*' => 'mimes:jpeg,jpg,png,gif,bmp,pcx', 'image'],
            'look_for' => ['required', 'array'],
            'look_for.*' => ['required_with:look_for', 'string', 'in:male,female'],
        ];
    }
}
