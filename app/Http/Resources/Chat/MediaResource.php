<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MediaResource
 * @package App\Http\Resources\User
 * @mixin Chat
 *
 * @OA\Schema(schema="MediaResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="full_url", type="string", example="/path/to/file", description="Photo full size"),
 *     @OA\Property(property="thumb_url", type="string", example="/path/to/file", description="Photo mini size"),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 * )
 */
class MediaResource extends JsonResource
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
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
        ];
    }
}
