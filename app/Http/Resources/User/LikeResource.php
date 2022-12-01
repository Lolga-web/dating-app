<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LikeResource
 * @package App\Http\Resources\Users
 * @mixin User
 *
 * @OA\Schema(schema="LikeResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="photo", ref="#/components/schemas/PhotoResource"),
 *     @OA\Property(property="user", ref="#/components/schemas/UserIndexResource"),
 * )
 */
class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $this->markViewed();
        $userPhoto = $this->user->getFirstMedia($this->user->mediaCollection, ['main' => true]);

        return [
            'id' => $this->getKey(),
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
            'photo' => PhotoResource::make($this->media),
            'user' => IndexResource::make($this->user),
        ];
    }
}
