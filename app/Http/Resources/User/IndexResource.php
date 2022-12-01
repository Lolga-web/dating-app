<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class IndexResource
 * @package App\Http\Resources\User
 * @mixin User
 *
 * @OA\Schema(schema="UserIndexResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Ivan Ivanov"),
 *     @OA\Property(property="gender", type="string", example="male/female"),
 *     @OA\Property(property="language", type="string", example="en"),
 *     @OA\Property(property="birthday", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="occupation", type="string", example="businessman"),
 *     @OA\Property(property="full_questionnaire", type="boolean"),
 *     @OA\Property(property="role", type="string", example="user / bot"),
 *     @OA\Property(property="blocked", type="boolean", description="if you blocked this user"),
 *     @OA\Property(property="allow_chat", type="boolean"),
 *     @OA\Property(property="is_online", type="boolean"),
 *     @OA\Property(property="last_online_at", type="date", description="if is_online false", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="distance", type="number", description="distance to user in km", example=9658.6801),
 *     @OA\Property(property="main_photo", ref="#/components/schemas/PhotoResource"),
 *     @OA\Property(property="location", ref="#/components/schemas/LocationResource"),
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
            'gender' => $this->gender,
            'language' => $this->language,
            'birthday' => $this->birthday ? $this->birthday->format("Y-m-d H:i:s") : null,
            'occupation' => $this->questionnaire->occupation,
            'full_questionnaire' => $this->questionnaire->fullness(),
            'role' => $this->roles()->first()->name,
            'blocked' => \Auth::user()->isBlocked($this->id),
            // 'block_messages' => $this->settings->block_messages,
            'allow_chat' => !$this->settings->block_messages || $this->hasMatch(\Auth::user()) || $this->hasAcceptedInvitations(\Auth::user()),
            'is_online' => $this->isOnline(),
            'last_online_at' => $this->when(!$this->isOnline(), $this->last_online_at->format("Y-m-d H:i:s")),
            'distance' => $this->when($this->distance !== null, $this->distance),
            'main_photo' => PhotoResource::make($this->getMedia($this->mediaCollection, ['main' => true])->first()),
            'location' => LocationResource::make($this->location),
        ];
    }
}
