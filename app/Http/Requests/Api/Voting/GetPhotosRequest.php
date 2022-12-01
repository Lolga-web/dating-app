<?php

namespace App\Http\Requests\Api\Voting;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class GetPhotosRequest
 * @package App\Http\Requests\Api\Voting
 *
 * @OA\Schema(schema="GetVotingPhotosRequest", required={"gender"},
 *     @OA\Property(property="gender", type="string", description="User gender", example="male / female"),
 *     @OA\Property(property="country", type="string", example="Germany"),
 * )
 */
class GetPhotosRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'gender' => ['required', 'string', 'in:male,female'],
            'country' => ['sometimes', 'string'],
        ];
    }
}
