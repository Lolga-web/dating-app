<?php

namespace App\Http\Requests\Api\Chat;

use App\Http\Requests\Api\ApiBaseRequest;

use App\Models\Chat;

class MessageDeleteRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'bail',
                function ($attribute, $value, $fail) {
                    if ($value->sender->isNot($this->user())) {
                        $fail(__("You can not delete this message"));
                        return;
                    }
                }
            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge(['message' => $this->route('message')]);
    }
}
