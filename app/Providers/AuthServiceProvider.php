<?php

namespace App\Providers;

use App\Grants\PhoneGrant;
use App\Grants\SocialGrant;
use DateInterval;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;
use Laravel\Passport\Bridge\RefreshTokenRepository;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @throws BindingResolutionException
     */
    public function boot()
    {
        $this->registerPolicies();

        app(AuthorizationServer::class)->enableGrantType(
            $this->makeSocialGrant(), new DateInterval('P1D')
        );

        app(AuthorizationServer::class)->enableGrantType(
            $this->makePhoneGrant(), new DateInterval('P1D')
        );

        Passport::routes(null, ['prefix' => 'api/v1/oauth']);
        Passport::tokensExpireIn(now()->addDays(1));
    }

    /**
     * Create and configure a Social grant instance.
     *
     * @return SocialGrant
     * @throws BindingResolutionException
     */
    protected function makeSocialGrant(): SocialGrant
    {
        $grant = new SocialGrant(
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }

    /**
     * Create and configure a Phone grant instance.
     *
     * @return PhoneGrant
     * @throws BindingResolutionException
     */
    protected function makePhoneGrant(): PhoneGrant
    {
        $grant = new PhoneGrant(
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
