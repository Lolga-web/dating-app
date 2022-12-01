<?php

namespace App\Http\Requests\Api\Chat;

use App\Http\Requests\Api\ApiBaseRequest;

/**
 * Class MessageStoreRequest
 * @package App\Http\Requests\Api\Chat
 *
 *  @OA\Schema(schema="MessageStoreRequest",
 *     @OA\Property(property="text", type="string", example="Hi!"),
 *     @OA\Property(property="media", type="file"),
 * )
 */
class MessageStoreRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'recipient_id' => ['required', 'numeric', 'exists:users,id'],
            'sender_id' => ['required', 'numeric', 'exists:users,id'],
            'parent_id' => ['sometimes', 'nullable', 'numeric', 'exists:chats,id'],
            'text' => ['required_without:image', 'string', 'max:256'],
            'image' => ['required_without:text', 'file' => 'max:5000', 'file.*' => 'mimes:jpeg,jpg,png,gif,bmp,pcx', 'image'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'sender_id' => \Auth::id(),
            'recipient_id' => $this->route('recipient'),
            'parent_id' => $this->route('parent'),
        ]);
    }
}
