<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class DeviceRequest
 * @package App\Http\Requests\Api\User
 *
 * @OA\Schema(schema="DeviceRequest", required={"type", "token"},
 *     @OA\Property(property="type", type="string", example="ios / android"),
 *     @OA\Property(property="token", type="string"),
 * )
 */
class DeviceRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:ios,android'],
            'token' => ['required', 'string'],
        ];
    }
}
