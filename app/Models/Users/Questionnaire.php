<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Questionnaire extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questionnaire';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purpose',
        'height',
        'weight',
        'eye_color',
        'hair_color',
        'hair_length',
        'marital_status',
        'kids',
        'education',
        'occupation',
        'about_me',
        'search_age_min',
        'search_age_max',
        'socials',
        'hobby',
        'sport',
        'evening_time',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'socials' => 'array',
        'hobby' => 'array',
        'sport' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
    * @return bool
    */
    public function fullness(): bool
    {
        return $this->purpose && $this->height && $this->weight && $this->eye_color &&
                $this->hair_color && $this->hair_length && $this->marital_status && $this->kids &&
                $this->education && $this->occupation && $this->about_me && $this->search_age_min && $this->search_age_max &&
                $this->socials && $this->hobby && $this->sport && $this->evening_time;
    }
}
