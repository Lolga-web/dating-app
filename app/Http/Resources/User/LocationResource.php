<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LocationResource
 * @package App\Http\Resources\User
 * @mixin User
 *
 * @OA\Schema(schema="LocationResource",
 *     @OA\Property(property="iso_code", type="string", example="DE"),
 *     @OA\Property(property="country", type="string", example="Germany"),
 *     @OA\Property(property="city", type="string", example="Berlin"),
 * )
 */
class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'iso_code' => $this->iso_code,
            'country' => $this->country,
            'city' => $this->city,
        ];
    }
}
