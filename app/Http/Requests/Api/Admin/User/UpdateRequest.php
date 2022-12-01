<?php

namespace App\Http\Requests\Api\Admin\User;

use App\Http\Requests\Api\ApiBaseRequest;
use App\Rules\CheckUserData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Api\Admin\User
 *
 * @OA\Schema(schema="AdminUserUpdateRequest",
 *     @OA\Property(property="email", type="string", description="User email", example="some_user@gmail.com"),
 *     @OA\Property(property="name", type="string", description="User name", example="Ivan Ivanov"),
 *     @OA\Property(property="password", type="string"),
 *     @OA\Property(property="password_confirmation", type="string"),
 *     @OA\Property(property="gender", type="string", description="User gender", example="male / female"),
 *     @OA\Property(property="language", type="string", description="Locale", example="en"),
 *     @OA\Property(property="birthday", type="date", example="2000-01-01", description="User birthday"),
 *     @OA\Property(property="delete_photos", type="array", @OA\Items(type="numeric"), example={1,2,3}),
 *     @OA\Property(property="look_for", type="array", @OA\Items(type="string"), example={"male","female"}),
 * )
 */
class UpdateRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request): array
    {
        $this->sanitize();

        return [
            'email' => [new CheckUserData($request, $this->user), 'nullable', 'email:rfc,dns', 'max:191', 'unique:users,email,'.$this->user->id],
            'phone' => ['nullable', Rule::phone()->detect(), 'max:64', 'unique:users,phone,'.$this->user->id],
            'name' => ['string', 'min:1', 'max:20'],
            'password' => ['string', 'min:6', 'confirmed'],
            'gender' => ['string', 'in:male,female'],
            'language' => ['string', 'in:'. implode(',', config('translatable.locales'))],
            'birthday' => ['date', 'date_format:Y-m-d', 'before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d')],
            'delete_photos' => ['array'],
            'delete_photos.*' => ['required_with:delete_photos', 'numeric', 'exists:media,id', 'bail',
                function ($attribute, $value, $fail) {
                    if (Media::find($value)->model_id !== $this->user->id) {
                        $fail(__("Id value is not correct"));
                        return;
                    }
                }
            ],
            'look_for' => ['array'],
            'look_for.*' => ['required_with:look_for', 'string', 'in:male,female'],
        ];
    }
}
