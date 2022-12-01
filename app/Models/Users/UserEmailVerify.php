<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEmailVerify extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_verifies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'email', 'code', 'confirmed', 'expires_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Builder $query
     * @param string $value
     *
     * @return Builder
     */
    public function scopeGetByValue(Builder $query, string $value): Builder
    {
        return $query->where('email', $value);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('confirmed', true);
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed == true;
    }

    public function markVerified()
    {
        return $this->update(['confirmed' => true]);
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function checkCode(string $code = null): bool
    {
        return ($this->code === $code && $this->expires_at > now());
    }
}
