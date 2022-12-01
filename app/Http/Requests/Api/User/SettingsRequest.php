<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiBaseRequest;

use Illuminate\Validation\Rule;

/**
 * Class SettingsRequest
 * @package App\Http\Requests\Api\User
 *
 *  @OA\Schema(schema="SettingsRequest",
 *     @OA\Property(property="block_messages", type="boolean", example=true),
 *     @OA\Property(property="matches", type="boolean", example=true),
 *     @OA\Property(property="invisible", type="boolean", example=true),
 *     @OA\Property(property="likes_notifications", type="boolean", example=true),
 *     @OA\Property(property="matches_notifications", type="boolean", example=true),
 *     @OA\Property(property="invitations_notifications", type="boolean", example=true),
 *     @OA\Property(property="messages_notifications", type="boolean", example=true),
 *     @OA\Property(property="guests_notifications", type="boolean", example=true),
 * )
 *
 * @OA\Schema(schema="SettingsLanguageRequest",
 *     @OA\Property(property="language", type="string", example="en"),
 * )
 */
class SettingsRequest extends ApiBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'block_messages' => ['sometimes', 'boolean'],
            'matches' => ['sometimes', 'boolean'],
            'invisible' => ['sometimes', 'boolean'],
            'likes_notifications' => ['sometimes', 'boolean'],
            'matches_notifications' => ['sometimes', 'boolean'],
            'invitations_notifications' => ['sometimes', 'boolean'],
            'messages_notifications' => ['sometimes', 'boolean'],
            'guests_notifications' => ['sometimes', 'boolean'],
            'language' => ['sometimes', 'string', 'in:'. implode(',', config('translatable.locales'))],
        ];
    }
}
