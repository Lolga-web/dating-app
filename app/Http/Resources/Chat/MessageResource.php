<?php

namespace App\Http\Resources\Chat;

use App\Models\Media;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MessageResource
 * @package App\Http\Resources\Chat
 * @mixin Chat
 *
 * @OA\Schema(schema="MessageResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="sender_id", type="integer", example=1),
 *     @OA\Property(property="recipient_id", type="integer", example=1),
 *     @OA\Property(property="parent_id", type="integer", example=1),
 *     @OA\Property(property="text", type="string", example="Hi!"),
 *     @OA\Property(property="image", ref="#/components/schemas/MediaResource"),
 *     @OA\Property(property="read_at", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 * )
 */
class MessageResource extends JsonResource
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
            'sender_id' => $this->sender_id,
            'recipient_id' => $this->recipient_id,
            'parent_id' => $this->parent_id,
            'text' => $this->text,
            'image' => MediaResource::make($this->getMedia($this->imageCollection)->first()),
            'read_at' => $this->read_at ? $this->read_at->format("Y-m-d H:i:s") : null,
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
        ];
    }
}
