<?php

namespace App\Http\Requests\Api\Match;

use App\Http\Requests\Api\ApiBaseRequest;

use App\Models\UserMatch;

use Illuminate\Validation\Rule;

/**
 * Class StoreRequest
 * @package App\Http\Requests\Api\Match
 *
 *  @OA\Schema(schema="MatchStoreRequest", required={"user_id"},
 *     @OA\Property(property="user_id", type="integer", example=1),
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
            'user_id' => ['required', 'numeric', 'bail',
                            Rule::exists('users', 'id')->where(function ($query) {
                                return $query->whereNotIn('id', [\Auth::id()]);
                            }),
                            function ($attribute, $value, $fail) {
                                if (UserMatch::where([['user_id', $value], ['from_user_id', \Auth::id()]])->first()) {
                                    $fail(__("Already matched"));
                                    return;
                                }
                            }
            ],
        ];
    }
}
