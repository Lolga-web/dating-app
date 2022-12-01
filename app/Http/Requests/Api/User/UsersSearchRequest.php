<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class UsersSearchRequest
 * @package App\Http\Requests\Api\User
 *
 *  @OA\Schema(schema="UsersSearchRequest", required={"location", "online"},
 *     @OA\Property(property="location", type="string", example="all | nearby | country | city"),
 *     @OA\Property(property="online", type="string", example="all | online | recently"),
 *     @OA\Property(property="filters", type="object",
 *         @OA\Property(property="location", type="object",
 *             @OA\Property(property="range", type="integer", example=5),
 *             @OA\Property(property="lat", type="string", example="41.31"),
 *             @OA\Property(property="lng", type="string", example="-72.92"),
 *         ),
 *     ),
 * )
 */
class UsersSearchRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'location' => ['required_without:filters.location', 'string', 'in:all,nearby,country,city'],
            'online' => ['required', 'string', 'in:all,online,recently'],
            'filters' => ['array', 'nullable'],
            'filters.location' => ['required_without:location', 'array', 'nullable'],
            'filters.location.range' => ['required_with:filters.location', 'integer'],
            'filters.location.lat' => ['required_with:filters.location', 'string'],
            'filters.location.lng' => ['required_with:filters.location', 'string'],
        ];
    }
}
