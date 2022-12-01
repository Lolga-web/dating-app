<?php

namespace App\Models\Invitations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

use App\Traits\Translatable;

use App\Models\BaseModel;

class InvitationAnswer extends BaseModel implements TranslatableContract
{
    use HasFactory, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invitation_answers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['affirmative'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'affirmative' => 'bool',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public array $translatedAttributes = ['name'];
}
