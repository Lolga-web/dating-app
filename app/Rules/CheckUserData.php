<?php

namespace App\Rules;

use App\Models\Users\User;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Http\Request;

class CheckUserData implements ImplicitRule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request, User $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $inputs = $this->request->input();

        if (array_key_exists('email', $inputs) && $inputs['email'] == null && array_key_exists('phone', $inputs) && $inputs['phone'] == null) {
            return false;
        } elseif (array_key_exists('email', $inputs) && !$this->request->input('email') && !$this->user->phone && !$this->request->input('phone')) {
            return false;
        } elseif (array_key_exists('phone', $inputs) && !$this->request->input('phone') && !$this->user->email && !$this->request->input('email')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Email or phone required');
    }
}
