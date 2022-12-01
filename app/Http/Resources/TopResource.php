<?php

namespace App\Http\Resources;

use App\Http\Resources\User\IndexResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TopResource
 * @package App\Http\Resources\User
 * @mixin User
 *
 * @OA\Schema(schema="TopResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="full_url", type="string", example="/path/to/file", description="Photo full size"),
 *     @OA\Property(property="thumb_url", type="string", example="/path/to/file", description="Photo mini size"),
 *     @OA\Property(property="rating", type="integer", example=100, description="Rating in percent"),
 *     @OA\Property(property="user", ref="#/components/schemas/UserIndexResource"),
 * )
 */
class TopResource extends JsonResource
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
            'full_url' => $this->getUrl(),
            'thumb_url' => $this->getUrl('thumb'),
            'rating' => $this->calcRating(),
            'user' => IndexResource::make($this->user),
        ];
    }
}
