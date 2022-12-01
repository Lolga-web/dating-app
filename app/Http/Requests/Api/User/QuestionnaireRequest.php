<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class QuestionnaireRequest
 * @package App\Http\Requests\Api\User
 *
 *  @OA\Schema(schema="QuestionnaireRequest",
 *     @OA\Property(property="purpose", type="string", description="Purpose of dating", example="love"),
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
 *     @OA\Property(property="search_age_min", type="integer", example=20),
 *     @OA\Property(property="search_age_max", type="integer", example=30),
 *     @OA\Property(property="socials", type="array", @OA\Items()),
 *     @OA\Property(property="hobby", type="array", @OA\Items()),
 *     @OA\Property(property="sport", type="array", @OA\Items()),
 *     @OA\Property(property="evening_time", type="string", example="sleeping"),
 * )
 */
class QuestionnaireRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'purpose' => ['string', 'nullable', 'max:191'],
            'expectations' => ['string', 'nullable', 'max:191'],
            'height' => ['integer', 'nullable'],
            'weight' => ['integer', 'nullable'],
            'eye_color' => ['string', 'nullable', 'max:191'],
            'hair_color' => ['string', 'nullable', 'max:191'],
            'hair_length' => ['string', 'nullable', 'max:191'],
            'marital_status' => ['string', 'nullable', 'max:191'],
            'kids' => ['string', 'nullable', 'max:191'],
            'education' => ['string', 'nullable', 'max:191'],
            'occupation' => ['string', 'nullable', 'max:191'],
            'about_me' => ['string', 'nullable', 'max:1000'],
            'nationality' => ['string', 'nullable', 'max:191'],
            'search_age_min' => ['integer', 'nullable'],
            'search_age_max' => ['integer', 'nullable'],
            'search_country' => ['string', 'nullable', 'max:191'],
            'search_city' => ['string', 'nullable', 'max:191'],
            'socials' => ['array', 'nullable'],
            'socials.*' => ['required_with:socials', 'string'],
            'hobby' => ['array', 'nullable'],
            'hobby.*' => ['required_with:hobby', 'string'],
            'sport' => ['array', 'nullable'],
            'sport.*' => ['required_with:sport', 'string'],
            'evening_time' => ['string', 'nullable', 'max:191'],
        ];
    }
}
