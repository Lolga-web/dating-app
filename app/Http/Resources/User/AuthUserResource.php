<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AuthUserResource
 * @package App\Http\Resources\Admin
 * @mixin User
 *
 * @OA\Schema(schema="AuthUserResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Ivan Ivanov"),
 *     @OA\Property(property="email", type="string", example="some_user@gmail.com"),
 *     @OA\Property(property="email_verification", type="boolean"),
 *     @OA\Property(property="phone", type="string", example="+79999999999"),
 *     @OA\Property(property="phone_verification", type="boolean"),
 *     @OA\Property(property="gender", type="string", example="male/female"),
 *     @OA\Property(property="look_for", type="array", @OA\Items(type="string"), example={"male","female"}),
 *     @OA\Property(property="language", type="string", example="en"),
 *     @OA\Property(property="birthday", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="coins", type="string", example="1"),
 *     @OA\Property(property="new_likes", type="integer", example=1),
 *     @OA\Property(property="new_guests", type="integer", example=1),
 *     @OA\Property(property="new_messages", type="integer", example=1),
 *     @OA\Property(property="new_invitations", type="integer", example=1),
 *     @OA\Property(property="new_duels", type="integer", example=1),
 *     @OA\Property(property="new_matches", type="integer", example=1),
 *     @OA\Property(property="photos", type="array", @OA\Items(ref="#/components/schemas/PhotoResource")),
 *     @OA\Property(property="location", ref="#/components/schemas/LocationResource"),
 *     @OA\Property(property="questionnaire", ref="#/components/schemas/QuestionnaireResource"),
 * )
 */
class AuthUserResource extends JsonResource
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
            'email' => $this->email,
            'email_verification' => $this->email_verified_at ? true : false,
            'phone' => $this->phone,
            'phone_verification' => $this->phone_verified_at ? true : false,
            'gender' => $this->gender,
            'look_for' => $this->look_for,
            'language' => $this->language,
            'birthday' => $this->birthday ? $this->birthday->format("Y-m-d H:i:s") : null,
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
            'coins' => $this->getWallet('coins-wallet')->balance,
            'new_likes' => $this->getLikes()->where('viewed', false)->count(),
            'new_guests' => $this->guests()->where('viewed', false)->count(),
            'new_messages' => $this->toMessages()->where('read_at', null)->count(),
            'new_invitations' => $this->receivedInvitations()->where('answer_id', null)->count(),
            'new_duels' => $this->getVotes()->where('viewed_by_winner', false)->count(),
            'new_matches' => $this->matches()->where('matched', true)->where('viewed', false)->count(),
            'photos' => PhotoResource::collection($this->getMedia($this->mediaCollection)->sortByDesc(function ($item, $key) {
                return $item->getCustomProperty('main');
            })),
            'location' => LocationResource::make($this->location),
            'questionnaire' => QuestionnaireResource::make($this->questionnaire),
        ];
    }
}
