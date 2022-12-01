<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDevices extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_devices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'type' => 'string',
        'type' => 'string',
    ];

}
