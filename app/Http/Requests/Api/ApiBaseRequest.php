<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ApiBaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     */
    protected function failedValidation(Validator $validator)
    {
        $message = (method_exists($this, 'message'))
            ? $this->container->call([$this, 'message'])
            : 'The given data was invalid.';
        if ($validator->stopOnFirstFailure()->fails()) {
            $message = $validator->errors()->first();
        }

        throw new HttpResponseException((new JsonResponse([
            'success' => false,
            'data'    => (object)[],
            'message' => $message,
            'errors'  => (new ValidationException($validator))->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY))->setStatusCode(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, !empty($message) ? $message : null));
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return Str::lower(trim($this->input('email')));
    }

    public function sanitize()
    {
        $inputs = $this->input();
        if (array_key_exists('email', $inputs) && $inputs['email'] !== null) {
            $inputs['email'] = Str::lower($inputs['email']);
        }
        $this->replace($inputs);
    }

    /**
     * Get confirm code
     *
     * @return int
     */
    public function getConfirmCode(): int
    {
        return (int) trim($this->input('code'));
    }
}
