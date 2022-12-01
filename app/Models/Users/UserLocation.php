<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jackpopp\GeoDistance\GeoDistanceTrait;

class UserLocation extends BaseModel
{
    use HasFactory, GeoDistanceTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ip',
        'iso_code',
        'country',
        'city',
        'state',
        'state_name',
        'postal_code',
        'lat',
        'lng',
        'timezone',
        'continent',
        'currency',
        'default'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
