<?php

namespace App\Http\Requests\Api\Chat;

use App\Http\Requests\Api\ApiBaseRequest;

class SendEventRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'recipient_id' => ['required', 'numeric', 'exists:users,id'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'recipient_id' => $this->route('recipient'),
        ]);
    }
}
