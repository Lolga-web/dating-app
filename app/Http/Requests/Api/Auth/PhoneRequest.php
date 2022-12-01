<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;

use Illuminate\Validation\Rule;

use App\Models\Users\User;

/**
 * Class PhoneRequest
 * @package App\Http\Requests\Api\Auth
 *
 * @OA\Schema(schema="PhoneRequest", required={"phone"},
 *     @OA\Property(property="phone", type="string", description="Input phone", example="+79999999999"),
 * )
 */
class PhoneRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', Rule::phone()->detect(), Rule::unique((new User())->getTable(), 'phone')],
        ];
    }

    public function messages()
    {
        return [
            "phone.phone" => __('Invalid phone number format'),
        ];
    }
}
