<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class LocationRequest
 * @package App\Http\Requests\Api\User
 *
 * @OA\Schema(schema="LocationRequest", required={"lat", "lng", "iso_code", "country", "city"},
 *     @OA\Property(property="lat", type="string", example="41.31"),
 *     @OA\Property(property="lng", type="string", example="-72.92"),
 *     @OA\Property(property="iso_code", type="string", example="US"),
 *     @OA\Property(property="country", type="string", example="United States"),
 *     @OA\Property(property="state", type="string", example="CT"),
 *     @OA\Property(property="state_name", type="string", example="Connecticut"),
 *     @OA\Property(property="city", type="string", example="New Haven"),
 * )
 */
class LocationRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'lat' => ['required', 'string', 'max:255'],
            'lng' => ['required', 'string', 'max:255'],
            'iso_code' => ['required', 'string', 'max:2'],
            'country' => ['required', 'string', 'max:255'],
            'state' => ['string', 'max:6', 'nullable'],
            'state_name' => ['string', 'max:255', 'nullable'],
            'city' => ['required', 'string', 'max:255'],
        ];
    }
}
