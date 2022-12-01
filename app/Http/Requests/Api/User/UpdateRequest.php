<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

use Illuminate\Support\Carbon;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Api\User
 *
 * @OA\Schema(schema="UserUpdateRequest",
 *     @OA\Property(property="name", type="string", description="User name", example="Ivan Ivanov"),
 *     @OA\Property(property="gender", type="string", description="User gender", example="male / female"),
 *     @OA\Property(property="language", type="string", description="Locale", example="en"),
 *     @OA\Property(property="birthday", type="date", example="2000-01-01", description="User birthday"),
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
    public function rules(): array
    {
        return [
            'name' => ['string', 'min:1', 'max:20'],
            'gender' => ['string', 'in:male,female'],
            'language' => ['string', 'in:'. implode(',', config('translatable.locales'))],
            'birthday' => ['date', 'date_format:Y-m-d', 'before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d')],
            'look_for' => ['array'],
            'look_for.*' => ['required_with:look_for', 'string', 'in:male,female'],
        ];
    }
}
