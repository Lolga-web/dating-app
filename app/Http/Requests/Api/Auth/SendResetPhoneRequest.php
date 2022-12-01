<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;

use Illuminate\Validation\Rule;

/**
 * Class SendResetPhoneRequest
 * @package App\Http\Requests\Api\Auth
 *
 * @OA\Schema(schema="SendResetPhoneRequest", required={"phone"},
 *     @OA\Property(property="phone", type="string", description="Input phone", example="+79999999999"),
 * )
 */
class SendResetPhoneRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', Rule::phone()->detect(), 'max:255', 'exists:users,phone'],
        ];
    }
}
