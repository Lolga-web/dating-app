<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class PhotoDestroyRequest
 * @package App\Http\Requests\Api\User
 *
 *  @OA\Schema(schema="PhotoDestroyRequest", required={"ids"},
 *     @OA\Property(property="ids", type="array", @OA\Items(type="numeric"), example={1,2,3}),
 * )
 */
class PhotoDestroyRequest extends ApiBaseRequest
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
            'ids.*' => ['numeric', 'exists:media,id', 'bail',
                function ($attribute, $value, $fail) {
                    $photo = Media::find($value);
                    if ($photo->model_id !== \Auth::id()) {
                        $fail(__("Id value is not correct"));
                        return;
                    }
                }
            ],
        ];
    }
}
