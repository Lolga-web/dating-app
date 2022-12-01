<?php

namespace App\Grants;

use App\Models\Users\User;
use App\Services\Users\UserService;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Grant\AbstractGrant;
use Laravel\Passport\Bridge\User as UserEntity;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class SocialGrant extends AbstractGrant
{
    /**
     * SocialGrant constructor.
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->setRefreshTokenRepository($refreshTokenRepository);

        $this->refreshTokenTTL = new \DateInterval('P1M');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseTypeInterface $responseType
     * @param \DateInterval $accessTokenTTL
     * @return ResponseTypeInterface
     * @throws OAuthServerException
     * @throws \League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ): ResponseTypeInterface {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request, $this->defaultScope));
        $user = $this->validateUser($request);

        // Finalize the requested scopes
        $finalizedScopes = $this->scopeRepository->finalizeScopes($scopes, 'password', $client, $user->getIdentifier());

        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $finalizedScopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        // Send events to emitter
        $this->getEmitter()->emit(new RequestEvent(RequestEvent::ACCESS_TOKEN_ISSUED, $request));
        $this->getEmitter()->emit(new RequestEvent(RequestEvent::REFRESH_TOKEN_ISSUED, $request));

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    /**
     * Validate server request and get the user entity.
     *
     * @param ServerRequestInterface $request
     * @return UserEntity
     * @throws OAuthServerException
     */
    public function validateUser(ServerRequestInterface $request): UserEntity
    {
        $provider = $this->getRequestParameter('provider', $request);
        if (is_null($provider)) {
            throw OAuthServerException::invalidRequest('provider');
        }

        $accessToken = $this->getRequestParameter('access_token', $request);
        if (is_null($accessToken)) {
            throw OAuthServerException::invalidRequest('access_token');
        }

        $type = $this->getRequestParameter('type', $request, 'web');

        $user = $this->resolveUserByProviderCredentials($provider, $accessToken, $type);
        if (is_null($user)) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));
            throw OAuthServerException::invalidCredentials();
        }

        return new UserEntity($user->getAuthIdentifier());
    }

    /**
     * Resolve user by provider credentials.
     *
     * @param string $provider
     * @param string $accessToken
     * @param string $type
     *
     * @return ?User
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken, string $type = 'web'): ?User
    {
        $providerUser = null;
        $socialiteDriver = Socialite::driver($provider);

        try {
            switch ($type) {
                case 'mobile';
                    $access_token = $socialiteDriver->getAccessTokenResponse($accessToken)['access_token'];
                    $providerUser = $socialiteDriver->userFromToken($access_token);
                    break;
                default:
                $providerUser = $socialiteDriver->scopes(['name', 'email'])->userFromToken($accessToken);
            }
        } catch (\Exception $exception) {
        }

        if ($providerUser) {
            return (new UserService())->socialAccountsCreate($providerUser, $provider, $type);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'social';
    }
}
