<?php

namespace App\Http\Resources;

use App\Http\Resources\User\IndexResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MatchResource
 * @package App\Http\Resources\
 * @mixin User
 *
 * @OA\Schema(schema="MatchResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="viewed", type="bollean", example=true),
 *     @OA\Property(property="user", ref="#/components/schemas/UserIndexResource"),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 * )
 */
class MatchResource extends JsonResource
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
            'viewed' => $this->viewed,
            'matched' => $this->matched,
            'user' => IndexResource::make($this->fromUser),
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),

        ];
    }
}
