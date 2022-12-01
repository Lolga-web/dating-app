<?php

namespace App\Models\Users;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Jackpopp\GeoDistance\GeoDistanceTrait;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWallets;
use Bavix\Wallet\Traits\CanPay;

use App\Builders\UserBuilder;

use App\Models\Chat;
use App\Models\Guest;
use App\Models\Invitations\UserInvitations;
use App\Models\Like;
use App\Models\PasswordResets;
use App\Models\UserMatch;
use App\Models\Voting;

use App\Traits\MustVerifyPhone;

class User extends Authenticatable implements MustVerifyEmail, HasMedia, Wallet
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, MustVerifyPhone, InteractsWithMedia, GeoDistanceTrait,
        HasWallet, HasWallets, CanPay;

    /**
     * Prop for ignore cats
     *
     * @var bool $preventAttrSet
     */
    public bool $preventAttrSet = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $guard_name = 'api';

    /**
     * The media library associated with the model.
     *
     * @var string
     */
    public string $mediaCollection = 'user_photo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'phone',
        'phone_verified_at',
        'gender',
        'look_for',
        'language',
        'birthday',
        'last_online_at',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'look_for' => 'array',
        'birthday' => 'datetime',
        'last_online_at' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function (self $user) {
            $user->setAttribute('last_online_at', now());
        });

        self::created(function (self $user) {
            $user->assignUserRole();
            $user->questionnaire()->create();
            $user->settings()->create();
            $user->location()->create($user->getLocation());
            $user->createWallet(['name' => 'Coins Wallet', 'slug' => 'coins-wallet']);
            $user->sendWelcomeMessage();
        });
    }

    /**
     * @return HasOne
     */
    public function userEmailVerify(): HasOne
    {
        return $this->hasOne(UserEmailVerify::class);
    }

    /**
     * @return HasOne
     */
    public function userPhoneVerify(): HasOne
    {
        return $this->hasOne(UserPhoneVerify::class);
    }

    /**
     * @return HasMany
     */
    public function passwordResets(): HasMany
    {
        return $this->hasMany(PasswordResets::class, 'user_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function questionnaire(): HasOne
    {
        return $this->hasOne(Questionnaire::class);
    }

    /**
     * @return HasOne
     */
    public function location(): HasOne
    {
        return $this->hasOne(UserLocation::class);
    }

    /**
     * @return HasMany
     */
    public function userSocialAccounts(): HasMany
    {
        return $this->hasMany(UserSocialAccount::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function devices(): HasMany
    {
        return $this->hasMany(UserDevices::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function toMessages(): HasMany
    {
        return $this->hasMany(Chat::class, 'recipient_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Voting::class, 'voter_id');
    }

    /**
     * @return HasMany
     */
    public function matches(): HasMany
    {
        return $this->hasMany(UserMatch::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function matchesFrom(): HasMany
    {
        return $this->hasMany(UserMatch::class, 'from_user_id');
    }

    /**
     * @return HasMany
     */
    public function receivedInvitations(): HasMany
    {
        return $this->hasMany(UserInvitations::class, 'to_user_id');
    }

    /**
     * @return HasMany
     */
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(UserInvitations::class, 'from_user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'user_id', 'blocked_user_id');
    }

    /**
     * @return HasOne
     */
    public function settings(): HasOne
    {
        return $this->hasOne(Settings::class);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param QueryBuilder $query
     *
     * @return Builder|static|UserBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new UserBuilder($query);
    }

    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if ($this->preventAttrSet) {
            $this->attributes['password'] = $value;
        } else {
            $this->attributes['password'] = Hash::make((string) $value, [PASSWORD_DEFAULT]);
        }
    }

    /**
     * Assign role 'user' to User.
     */
    public function assignUserRole()
    {
        $this->assignRole('user');
    }

    /**
     * Reset user password.
     *
     * @param $password
     */
    public function resetPassword(string $password)
    {
        $this->update(['password' => $password]);
    }

    /**
    * @return bool
    */
    public function isOnline()
    {
        $last_online = new Carbon($this->last_online_at);
        $online = $last_online->diffInMinutes(now()) < 5 && $this->last_online_at !== null ? true : false;
        return $online;
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
    * @return bool
    */
    public function hasPhotos(): bool
    {
        return $this->getFirstMedia($this->mediaCollection) ? true : false;
    }

    /**
    * @return bool
    */
    public function hasMainPhoto(): bool
    {
        return $this->getFirstMedia($this->mediaCollection, ['main' => true]) ? true : false;
    }

    /**
    * @return bool
    */
    public function hasMatch(User $user): bool
    {
        return $this->matches()->where([['from_user_id', $user->id], ['matched', true]])->exists();
    }

    /**
    * @return bool
    */
    public function hasAcceptedInvitations(User $user): bool
    {
        return $this->receivedInvitations()
                    ->where('from_user_id', $user->id)
                    ->whereHas('answer', function (Builder $query) {
                        $query->where('affirmative', true);
                    })
                    ->exists() ||
                $this->sentInvitations()
                    ->where('to_user_id', $user->id)
                    ->whereHas('answer', function (Builder $query) {
                        $query->where('affirmative', true);
                    })
                    ->exists();
    }

    /**
    * @return void
    */
    public function getLikes()
    {
        return Like::whereIn('media_id', $this->media()->pluck('id'));
    }

    /**
    * @return bool
    */
    public function isVoted(int $media): bool
    {
        return $this->votes()->where('winning_photo', $media)->first() ? true : false;
    }

    /**
    * @return void
    */
    public function getVotes()
    {
        return Voting::whereIn('winning_photo', $this->getMedia($this->mediaCollection)->pluck('id'));
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function addDistance(User $user): void
    {
        $this->setAttribute('distance', $this->getDistanceBetweenPoints(
            $user->location->lat, $user->location->lng, $this->location->lat, $this->location->lng
        ));
    }

    /**
     * @param string $lat1
     * @param string $lng1
     * @param string $lat2
     * @param string $lng2
     *
     * @return float
     */
    public function getDistanceBetweenPoints(string $lat1, string $lng1, string $lat2, string $lng2): float
    {
        $kilometers = (
            rad2deg(
                acos(
                    (sin(deg2rad((float)$lat1)) * sin(deg2rad((float)$lat2))) +
                    (
                        cos(deg2rad((float)$lat1)) *
                        cos(deg2rad((float)$lat2)) *
                        cos(deg2rad((float)$lng1 - (float)$lng2))
                    )
                )
            ) * 60 * 1.1515
        ) * 1.609344;

        if(is_nan($kilometers)) $kilometers = null;

        return round($kilometers, 4);
    }

    /**
    * @return bool
    */
    public function isBlocked(int $user): bool
    {
        return $this->blockedUsers()->where('blocked_user_id', $user)->first() ? true : false;
    }

    /**
    * @return void
    */
    public function getInvitations($filter)
    {
        switch ($filter) {
            case 'new':
                return $this->receivedInvitations()->where('answer_id', null);
                break;
            case 'answered':
                return $this->receivedInvitations()->whereNotNull('answer_id');
                break;
            case 'sent':
                return $this->sentInvitations();
                break;
        }
    }

    /**
     * Data for welcome message
     *
     * @return array
     */
    public function sendWelcomeMessage()
    {
        $bot = self::role('bot')->firstWhere('email', 'info@***.info');

        if ($bot) {
            $message = [
                'sender_id' => $bot->id,
                'recipient_id' => $this->id,
                'text' => __('Welcome to ***!')
            ];

            Chat::create($message);
        }
    }

    /**
     * Data for welcome message
     *
     * @return array
     */
    public function getLocation()
    {
        $locationData = geoip()->getLocation(geoip()->getLocation(\request()->ip())->id);

        return [
            'ip' => $locationData->ip,
            'lat' => $locationData->lat,
            'lng' => $locationData->lon,
            'postal_code' => $locationData->postal_code,
            'iso_code' => $locationData->iso_code,
            'country' => $locationData->country,
            'state' => $locationData->state,
            'state_name' => $locationData->state_name,
            'city' => $locationData->city,
        ];
    }

    /**
    * @return void
    */
    public function depositCoin()
    {
        return $this->getWallet('coins-wallet')->deposit(1, ['source' => 'voiting']);
    }

    /**
    * @return bool
    */
    public function canNotify(string $type): bool
    {
        switch ($type) {
            case 'message':
                return $this->settings->messages_notifications;
                break;
            case 'like':
                return $this->settings->likes_notifications;
                break;
            case 'guest':
                return $this->settings->guests_notifications;
                break;
            case 'invitation':
                return $this->settings->invitations_notifications;
                break;
            case 'match':
                return $this->settings->matches_notifications;
                break;
        }
    }

}
