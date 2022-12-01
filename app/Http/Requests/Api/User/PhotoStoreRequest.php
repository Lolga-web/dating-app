<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

class PhotoStoreRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'photo' => ['required', 'file' => 'max:5000', 'file.*' => 'mimes:jpeg,jpg,png,gif,bmp,pcx', 'image',
                function ($attribute, $value, $fail) {
                    $user = $this->user();
                    $photos = $user->getMedia($user->mediaCollection);
                    if ($photos->count() >= 10) {
                        $fail(__("Maximum number of photos exceeded"));
                        return;
                    }
                }
            ],
        ];
    }
}
