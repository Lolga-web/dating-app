<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ShowResource
 * @package App\Http\Resources\User
 * @mixin User
 *
 * @OA\Schema(schema="UserShowResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Ivan Ivanov"),
 *     @OA\Property(property="gender", type="string", example="male/female"),
 *     @OA\Property(property="look_for", type="array", @OA\Items(type="string"), example={"male","female"}),
 *     @OA\Property(property="language", type="string", example="en"),
 *     @OA\Property(property="birthday", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="role", type="string", example="user / bot"),
 *     @OA\Property(property="is_online", type="boolean"),
 *     @OA\Property(property="last_online_at", type="date", description="if is_online false", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="blocked", type="boolean", description="if you blocked this user",),
 *     @OA\Property(property="blocked_me", type="boolean", description="if user blocked you",),
 *     @OA\Property(property="allow_chat", type="boolean"),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="photos", type="array", @OA\Items(ref="#/components/schemas/PhotoResource")),
 *     @OA\Property(property="location", ref="#/components/schemas/LocationResource"),
 *     @OA\Property(property="questionnaire", ref="#/components/schemas/QuestionnaireResource"),
 * )
 */
class ShowResource extends JsonResource
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
            'look_for' => $this->look_for,
            'language' => $this->language,
            'birthday' => $this->birthday ? $this->birthday->format("Y-m-d H:i:s") : null,
            'role' => $this->roles()->first()->name,
            'is_online' => $this->isOnline(),
            'last_online_at' => $this->when(!$this->isOnline(), $this->last_online_at->format("Y-m-d H:i:s")),
            'blocked' => \Auth::user()->isBlocked($this->id),
            'blocked_me' => $this->isBlocked(\Auth::id()),
            // 'block_messages' => $this->settings->block_messages,
            'allow_chat' => !$this->settings->block_messages || $this->hasMatch(\Auth::user()) || $this->hasAcceptedInvitations(\Auth::user()),
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
            'photos' => PhotoResource::collection($this->getMedia($this->mediaCollection)->sortByDesc(function ($item, $key) {
                return $item->getCustomProperty('main');
            })),
            'location' => LocationResource::make($this->location),
            'questionnaire' => QuestionnaireResource::make($this->questionnaire),
        ];
    }
}
