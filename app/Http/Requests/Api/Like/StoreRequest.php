<?php

namespace App\Http\Requests\Api\Like;

use App\Http\Requests\Api\ApiBaseRequest;
use App\Models\Like;
use Illuminate\Validation\Rule;

/**
 * Class StoreRequest
 * @package App\Http\Requests\Api\User
 *
 *  @OA\Schema(schema="LikeStoreRequest", required={"photo_id"},
 *     @OA\Property(property="photo_id", type="integer", example=1),
 * )
 */
class StoreRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'photo_id' => ['required', 'numeric', 'bail',
                            Rule::exists('media', 'id')->where(function ($query) {
                                return $query->whereNotIn('model_id', [\Auth::id()]);
                            }),
                            // function ($attribute, $value, $fail) {
                            //     if (Like::where([['media_id', $value], ['from_user_id', \Auth::id()]])->first()) {
                            //         $fail(__("Already liked"));
                            //         return;
                            //     }
                            // }
            ],
        ];
    }
}
