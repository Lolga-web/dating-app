<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\User\IndexResource as UserIndexResource;
use App\Models\Chat;
use App\Models\Users\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class IndexResource
 * @package App\Http\Resources\Chat
 * @mixin Chat
 *
 * @OA\Schema(schema="ChatIndexResource",
 *     @OA\Property(property="user", ref="#/components/schemas/UserIndexResource"),
 *     @OA\Property(property="last_message", ref="#/components/schemas/MessageResource"),
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
            'user' => UserIndexResource::make($this),
            'last_message' => MessageResource::make($this->last_message),
        ];
    }
}
