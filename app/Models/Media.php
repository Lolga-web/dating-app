<?php

namespace App\Models;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media as ModelsMedia;

class Media extends ModelsMedia
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'model_id');
    }

    /**
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'media_id');
    }

    /**
     * @return HasMany
     */
    public function winning(): HasMany
    {
        return $this->hasMany(Voting::class, 'winning_photo');
    }

    /**
     * @return HasMany
     */
    public function losing(): HasMany
    {
        return $this->hasMany(Voting::class, 'loser_photo');
    }

    public function calcRating()
    {
        return $this->winning->count() * 100 / ($this->winning->count() + $this->losing->count());
    }

    public function changeMainStatus(bool $status)
    {
        $user = \Auth::user();

        switch ($status) {
            case true:
                $user->getFirstMedia($user->mediaCollection, ['main' => true])
                     ->setCustomProperty('main', false)
                     ->save();
                $this->setCustomProperty('main', true)
                     ->save();
                break;
            case false:
                $falsePhoto = $user->getFirstMedia($user->mediaCollection, ['main' => false]);
                if ($falsePhoto) {
                    $falsePhoto->setCustomProperty('main', true)
                               ->save();
                    $this->setCustomProperty('main', false)
                        ->save();
                };
                break;
        }
    }

    public function getByRating()
    {
        return $this->where('collection_name', 'user_photo')
                    ->has('winning')
                    ->get()
                    ->filter(function ($item) {
                            return (
                                $item->getCustomProperty('top') &&
                                $item->winning->count() >= 10 &&
                                $item->user->gender == \Auth::user()->gender &&
                                $item->setAttribute('rating', $item->calcRating())
                            );
                    })
                    ->sortByDesc('rating');
    }
}
