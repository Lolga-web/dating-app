<?php

namespace App\Http\Requests\Api\Invitation;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class IndexRequest
 * @package App\Http\Requests\Api\Invitation
 *
 * @OA\Schema(schema="InvitationIndexRequest", required={"filter"},
 *     @OA\Property(property="filter", type="string", example="new / answered / sent"),
 * )
 */
class IndexRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'filter' => ['required', 'string', 'in:new,answered,sent'],
        ];
    }
}
