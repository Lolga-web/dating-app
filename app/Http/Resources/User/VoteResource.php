<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class VoteResource
 * @package App\Http\Resources\User
 * @mixin User
 *
 * @OA\Schema(schema="VoteResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="winning_photo", ref="#/components/schemas/PhotoResource"),
 *     @OA\Property(property="loser_photo", ref="#/components/schemas/PhotoResource"),
 *     @OA\Property(property="voter", ref="#/components/schemas/UserIndexResource"),
 * )
 */
class VoteResource extends JsonResource
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
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
            'winning_photo' => PhotoResource::make($this->winningPhoto),
            'loser_photo' => PhotoResource::make($this->loserPhoto),
            'voter' => IndexResource::make($this->voter),
        ];
    }
}
