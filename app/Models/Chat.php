<?php

namespace App\Models;

use App\Models\Users\User;

use App\Builders\ChatBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Chat extends BaseModel implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chats';

    /**
     * The media library associated with the model.
     *
     * @var string
     */
    public string $imageCollection = 'chat_image';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'parent_id',
        'media_id',
        'text',
        'read_at',
        'deleted_by'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sender_id' => 'integer',
        'recipient_id' => 'integer',
        'parent_id' => 'integer',
        'media_id' => 'integer',
        'read_at'  => 'datetime',
        'deleted_by' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    /**
     * @return HasOne
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Chat::class, 'parent_id', 'id');
    }

    /**
     * Create a media collection for storing photos
     *
     * @return void
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(200)
              ->height(200)
              ->nonQueued();
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param QueryBuilder $query
     *
     * @return Builder|static|ChatBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new ChatBuilder($query);
    }

    /**
    * @return bool
    */
    public function isDeleted(User $user): bool
    {
        return $this->deletedBy()->is($user) ? true : false;
    }

}
