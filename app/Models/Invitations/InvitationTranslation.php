<?php

namespace App\Models\Invitations;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvitationTranslation extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invitations_translations';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    protected $fillable = ['name', 'locale'];
}
