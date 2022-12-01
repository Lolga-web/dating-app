<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Settings extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_settings';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'block_messages',
        'matches',
        'invisible',
        'likes_notifications',
        'matches_notifications',
        'invitations_notifications',
        'messages_notifications',
        'guests_notifications',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'block_messages' => 'bool',
        'matches' => 'bool',
        'invisible' => 'bool',
        'likes_notifications' => 'bool',
        'matches_notifications' => 'bool',
        'invitations_notifications' => 'bool',
        'messages_notifications' => 'bool',
        'guests_notifications' => 'bool',
    ];
}
