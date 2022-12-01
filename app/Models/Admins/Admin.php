<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * Prop for ignore cats
     *
     * @var bool $preventAttrSet
     */
    public bool $preventAttrSet = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admins';

    protected $guard_name = 'api-admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'language',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if ($this->preventAttrSet) {
            $this->attributes['password'] = $value;
        } else {
            $this->attributes['password'] = Hash::make((string) $value, [PASSWORD_DEFAULT]);
        }
    }
}
