<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

use App\Models\Guest;

/**
 * Class GuestViewedRequest
 * @package App\Http\Requests\Api\User
 *
 *  @OA\Schema(schema="GuestViewedRequest", required={"ids"},
 *     @OA\Property(property="ids", type="array", @OA\Items(type="numeric"), example={1,2,3}),
 * )
 */
class GuestViewedRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['numeric', 'exists:guests,id', 'bail',
                function ($attribute, $value, $fail) {
                    $guest = Guest::find($value);
                    if ($guest->user_id !== \Auth::id()) {
                        $fail(__("Id value is not correct"));
                        return;
                    }
                }
            ],
        ];
    }
}
