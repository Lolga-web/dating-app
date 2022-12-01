<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class IndexResource
 * @package App\Http\Resources\Admin
 * @mixin User
 *
 * @OA\Schema(schema="AdminUserIndexResource",
 *     @OA\Property(property="id", type="integer", example=222),
 *     @OA\Property(property="name", type="string", example="Ivan Ivanov"),
 *     @OA\Property(property="email", type="string", example="some_user@gmail.com"),
 *     @OA\Property(property="phone", type="string", example="+79187873168"),
 *     @OA\Property(property="gender", type="string", example="male/female"),
 *     @OA\Property(property="language", type="string", example="en"),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 * )
 */
class IndexResource extends JsonResource
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
            'id' => $this->getKey(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'language' => $this->language,
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
        ];
    }
}
