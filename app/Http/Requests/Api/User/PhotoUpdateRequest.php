<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class PhotoUpdateRequest
 * @package App\Http\Requests\Api\User
 *
 *  @OA\Schema(schema="PhotoUpdateRequest", required={"main", "top", "match"},
 *     @OA\Property(property="main", type="boolean", example=true),
 *     @OA\Property(property="top", type="boolean", example=true),
 * )
 */
class PhotoUpdateRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'photo' => ['required', 'bail',
                function ($attribute, $value, $fail) {
                    if ($this->user()->isNot($this->route('photo')->user)) {
                        $fail(__("Id value is not correct"));
                        return;
                    }
                }
            ],
            'main' => ['required', 'boolean'],
            'top' => ['required', 'boolean'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge(['photo' => $this->route('photo')]);
    }
}
