<?php

namespace App\Http\Requests\Api\Admin\User;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class DestroyRequest
 * @package App\Http\Requests\Api\Chat
 *
 *  @OA\Schema(schema="AdninUserDestroyRequest", required={"ids"},
 *     @OA\Property(property="ids", type="array", @OA\Items(type="numeric"), example={1,2,3}),
 * )
 */
class DestroyRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['numeric', 'exists:users,id'],
        ];
    }
}
