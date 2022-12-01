<?php

namespace App\Http\Resources\User;

use App\Models\Users\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GuestResource
 * @package App\Http\Resources\Users
 * @mixin User
 *
 * @OA\Schema(schema="GuestResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="visit_at", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="viewed", type="bollean", example=true),
 *     @OA\Property(property="guest", ref="#/components/schemas/UserIndexResource"),
 * )
 */
class GuestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        // $this->markViewed();

        return [
            'id' => $this->getKey(),
            'visit_at' => $this->visit_at->format("Y-m-d H:i:s"),
            'viewed' => $this->viewed,
            'guest' => IndexResource::make(User::find($this->guest_id)),
        ];
    }
}
