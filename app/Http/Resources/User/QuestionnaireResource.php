<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class QuestionnaireResource
 * @package App\Http\Resources\User
 * @mixin User
 *
 * @OA\Schema(schema="QuestionnaireResource",
 *     @OA\Property(property="purpose", type="string", description="Purpose of dating", example="love"),
 *     @OA\Property(property="expectations", type="string", description="Expectations from dating", example="find a wife"),
 *     @OA\Property(property="height", type="integer", example=170),
 *     @OA\Property(property="weight", type="integer", example=60),
 *     @OA\Property(property="eye_color", type="string", example="blue"),
 *     @OA\Property(property="hair_color", type="string", example="blond"),
 *     @OA\Property(property="hair_length", type="string", example="long"),
 *     @OA\Property(property="marital_status", type="string", example="married"),
 *     @OA\Property(property="kids", type="string", example="1"),
 *     @OA\Property(property="education", type="string", example="bachelor"),
 *     @OA\Property(property="occupation", type="string", example="businessman"),
 *     @OA\Property(property="about_me", type="string", example="I'm a crazy person"),
 *     @OA\Property(property="nationality", type="string", example="Germany"),
 *     @OA\Property(property="search_age_min", type="integer", example=20),
 *     @OA\Property(property="search_age_max", type="integer", example=30),
 *     @OA\Property(property="search_country", type="string", example="Germany"),
 *     @OA\Property(property="search_city", type="string", example="Berlin"),
 *     @OA\Property(property="socials", type="array", @OA\Items()),
 *     @OA\Property(property="hobby", type="array", @OA\Items()),
 *     @OA\Property(property="sport", type="array", @OA\Items()),
 *     @OA\Property(property="evening_time", type="string", example="sleeping"),
 * )
 */
class QuestionnaireResource extends JsonResource
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
            'purpose' => $this->purpose,
            'expectations' => $this->expectations,
            'height' => $this->height,
            'weight' => $this->weight,
            'eye_color' => $this->eye_color,
            'hair_color' => $this->hair_color,
            'hair_length' => $this->hair_length,
            'marital_status' => $this->marital_status,
            'kids' => $this->kids,
            'education' => $this->education,
            'occupation' => $this->occupation,
            'about_me' => $this->about_me,
            'nationality' => $this->nationality,
            'search_age_min' => $this->search_age_min,
            'search_age_max' => $this->search_age_max,
            'search_country' => $this->search_country,
            'search_city' => $this->search_city,
            'socials' => $this->socials,
            'hobby' => $this->hobby,
            'sport' => $this->sport,
            'evening_time' => $this->evening_time,
        ];
    }
}
