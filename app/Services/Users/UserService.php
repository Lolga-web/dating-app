<?php
declare(strict_types=1);

namespace App\Services\Users;

use App\Models\Users\User;
use App\Models\Users\UserSocialAccount;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Laravel\Socialite\Two\User as ProviderUser;
use Faker\Factory;

class UserService
{

    public array $nonWritableFields = [
        'email_verified_at',
        'phone_verified_at',
        'created_at',
        'updated_at',
        'email',
        'password',
        'id',
    ];

    /**
     * @return int
     */
    public function generateConfirmCode(): int
    {
        return rand(100000, 999999);
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function findByEmail(string $email): ?User
    {
        return User::firstWhere('email', $email);
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function findByPhone(string $phone): ?User
    {
        return User::firstWhere('phone', $phone);
    }

    /**
     * Find or create user instance by provider user instance and provider name.
     *
     * @param ProviderUser $providerUser
     * @param string $provider
     * @param string $type
     *
     * @return User
     */
    public function socialAccountsCreate(ProviderUser $providerUser, string $provider, string $type = 'web'): User
    {
        /** @var UserSocialAccount $userSocialAccount */
        $userSocialAccount = UserSocialAccount::query()
            ->where('provider_name', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($userSocialAccount) {
            return $userSocialAccount->user;
        }

        /** @var User $user */
        $user = null;

        if ($email = $providerUser->getEmail()) {
            $user = User::query()->where('email', $email)->first();
        }

        if (!$user) {
            switch ($provider) {
                case 'google':
                    $user = User::query()->create([
                        'email' => $email,
                        'name' => $providerUser->offsetGet('given_name'),
                        'language' => app()->getLocale(),
                    ]);
                    break;
                default:
                    $user = User::query()->create([
                        'email' => $email,
                        'name' => $providerUser->getName(),
                        'language' => app()->getLocale(),
                    ]);
            }
        }
        if ($email) {
            $user->markEmailAsVerified();
        }
        $user->save();

        $user->userSocialAccounts()->create([
            'provider_id'   => $providerUser->getId(),
            'provider_name' => $provider,
        ]);

        return $user;
    }

    public function setLocation(Request $request, User $user)
    {
        $ip = $request->ip();
        if (Str::substr($ip, 0, 7) == '127.0.0') {
            $ip = Factory::create()->ipv4;
        }

        $locationData = $request->validated();
        $locationData['ip'] = $ip;
        $locationData['user_id'] = $user->id;

        $user->location()->delete();
        $user->location()->create($locationData);
    }
}
