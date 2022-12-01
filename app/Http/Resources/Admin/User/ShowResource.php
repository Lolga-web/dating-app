<?php

namespace App\Http\Resources\Admin\User;

use App\Http\Resources\User\PhotoResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ShowResource
 * @package App\Http\Resources\Admin
 * @mixin User
 *
 * @OA\Schema(schema="AdminUserShowResource",
 *     @OA\Property(property="id", type="integer", example=222),
 *     @OA\Property(property="name", type="string", example="Ivan Ivanov"),
 *     @OA\Property(property="email", type="string", example="some_user@gmail.com"),
 *     @OA\Property(property="phone", type="string", example="+79187873168"),
 *     @OA\Property(property="gender", type="string", example="male/female"),
 *     @OA\Property(property="look_for", type="array", @OA\Items(type="string"), example={"male","female"}),
 *     @OA\Property(property="language", type="string", example="en"),
 *     @OA\Property(property="birthday", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="last_online_at", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 *     @OA\Property(property="photos", type="array",
 *          @OA\Items(
 *               ref="#/components/schemas/PhotoResource"
 *          )),
 *     @OA\Property(property="purpose", type="string", example="friendship/love/communication/sex"),
 *     @OA\Property(property="height", type="integer", example=170),
 *     @OA\Property(property="weight", type="integer", example=60),
 *     @OA\Property(property="eye_color", type="string", example="blue/gray/green/brown-yellow/yellow/brown/black"),
 *     @OA\Property(property="hair_color", type="string", example="blond/brown/brunette/redhead/no-hair"),
 *     @OA\Property(property="hair_length", type="string", example="short/medium/long/no-hair"),
 *     @OA\Property(property="marital_status", type="string", example="married/divorced/single/complicated/in-love/engaged/in-search"),
 *     @OA\Property(property="kids", type="string", example="1")),
 *     @OA\Property(property="education", type="string", example="...."),
 *     @OA\Property(property="occupation", type="string", example="student/working/unemployed/businessman/freelancer/in-search"),
 *     @OA\Property(property="about_me", type="string"),
 *     @OA\Property(property="search_age_min", type="integer", example=20),
 *     @OA\Property(property="search_age_max", type="integer", example=40),
 *     @OA\Property(property="socials", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="hobby", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="sport", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="evening_time", type="string", example="Walking around the city"),
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
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'look_for' => $this->look_for,
            'language' => $this->language,
            'birthday' => $this->birthday ? $this->birthday->format("Y-m-d H:i:s") : null,
            'last_online_at' => $this->last_online_at ? $this->last_online_at->format("Y-m-d H:i:s") : null,
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
            'photos' => PhotoResource::collection($this->getMedia($this->mediaCollection)),

            'purpose' => $this->questionnaire->purpose,
            'height' => $this->questionnaire->height,
            'weight' => $this->questionnaire->weight,
            'eye_color' => $this->questionnaire->eye_color,
            'hair_color' => $this->questionnaire->hair_color,
            'hair_length' => $this->questionnaire->hair_length,
            'marital_status' => $this->questionnaire->marital_status,
            'kids' => $this->questionnaire->kids,
            'education' => $this->questionnaire->education,
            'occupation' => $this->questionnaire->occupation,
            'about_me' => $this->questionnaire->about_me,
            'search_age_min' => $this->questionnaire->search_age_min,
            'search_age_max' => $this->questionnaire->search_age_max,
            'socials' => $this->questionnaire->socials,
            'hobby' => $this->questionnaire->hobby,
            'sport' => $this->questionnaire->sport,
            'evening_time' => $this->questionnaire->evening_time,
        ];
    }
}
