<?php

namespace App\Http\Requests\Api\Voting;

use App\Http\Requests\Api\ApiBaseRequest;
use App\Models\Media;
use Illuminate\Validation\Rule;

/**
 * Class StoreRequest
 * @package App\Http\Requests\Api\Voting
 *
 *  @OA\Schema(schema="VotingStoreRequest", required={"winning_photo", "loser_photo"},
 *     @OA\Property(property="winning_photo", type="integer", example=1),
 *     @OA\Property(property="loser_photo", type="integer", example=1),
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
            'winning_photo' => ['required', 'numeric', 'bail',
                Rule::exists('media', 'id')->where(function ($query) {
                    return $query->whereNotIn('model_id', [\Auth::id()]);
                }),
                function ($attribute, $value, $fail) {
                    if (\Auth::user()->isVoted($value)) {
                        $fail(__("Already voted"));
                        return;
                    }
                },
                function ($attribute, $value, $fail) {
                    if (!Media::find($value)->getCustomProperty('top')) {
                        $fail(__("Photo does not participate in voting"));
                        return;
                    }
                }
            ],
            'loser_photo' => ['required', 'numeric', 'bail',
                Rule::exists('media', 'id')->where(function ($query) {
                    return $query->whereNotIn('model_id', [\Auth::id()]);
                }),
                function ($attribute, $value, $fail) {
                    if (!Media::find($value)->getCustomProperty('top')) {
                        $fail(__("Photo does not participate in voting"));
                        return;
                    }
                }
            ],
        ];
    }
}
