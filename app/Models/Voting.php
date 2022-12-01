<?php

namespace App\Models;

use App\Models\Users\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voting extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'voting';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'winning_photo',
        'loser_photo',
        'voter_id',
        'viewed_by_winner'
    ];

    /**
     * @return BelongsTo
     */
    public function voter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voter_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function winningPhoto(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'winning_photo', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function loserPhoto(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'loser_photo', 'id');
    }
}
