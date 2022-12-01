<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

class BlockedRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user' => [
                function ($attribute, $value, $fail) {
                    if (\Auth::user()->blockedUsers()->where('blocked_user_id', $value->id)->exists()) {
                        $fail(__("Already blocked"));
                        return;
                    };
                    if ($value->id == \Auth::id()) {
                        $fail(__("Id value is not correct"));
                        return;
                    }
                }
            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge(['user' => $this->route('user')]);
    }
}
