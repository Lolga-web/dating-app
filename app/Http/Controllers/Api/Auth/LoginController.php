<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;

use App\Http\Controllers\Api\ApiBaseController;

use Laravel\Passport\TokenRepository;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Propaganistas\LaravelPhone\PhoneNumber;

use Illuminate\Http\Response;
use Illuminate\Support\Str;

use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

use App\Models\Users\User;
use App\Models\Users\UserSocialAccount;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends AccessTokenController
{
    /** @var APIBaseController $baseController */
    protected ApiBaseController $baseController;

    /**
     * Create a new controller instance.
     *
     * @param AuthorizationServer $server
     * @param TokenRepository $tokens
     * @param JwtParser $jwt
     * @param APIBaseController $baseController
     */
    public function __construct(
        AuthorizationServer $server,
        TokenRepository $tokens,
        JwtParser $jwt,
        APIBaseController $baseController
    ) {
        parent::__construct($server, $tokens, $jwt);

        $this->baseController = $baseController;
    }

    /**
     * Authorize a client to access the user's account.
     *
     * @OA\Post(
     *     path="/login-email",
     *     operationId="login-email",
     *     tags={"oAuth"},
     *     summary="Authentication by email",
     *     description="

### Example URI
**POST** https://your-website.com/api/v1/login-email",

     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"username", "password", "grant_type"},
     *                  type="object",
     *                  @OA\Property(property="username", type="string", description="User email", example="some_user@gmail.com"),
     *                  @OA\Property(property="password", type="string", description="User password", example="some_user@gmail.com"),
     *                  @OA\Property(property="grant_type", type="string", description="oAuth grant type", example="password"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Successful auth login",
     *          @OA\JsonContent(
     *              @OA\Property(property="token_type", type="string"),
     *              @OA\Property(property="expires_in", type="integer"),
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="refresh_token", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors** Could not create token.",
     *     ),
     * )
     *
     * @OA\Post(
     *     path="/login-phone",
     *     operationId="login-phone",
     *     tags={"oAuth"},
     *     summary="Authentication by phone",
     *     description="

### Example URI
**POST** https://your-website.com/api/v1/login-phone",

     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"phone", "password", "grant_type"},
     *                  type="object",
     *                  @OA\Property(property="phone", type="string", description="User phone", example="+79999999999"),
     *                  @OA\Property(property="password", type="string", description="User password"),
     *                  @OA\Property(property="grant_type", type="string", description="oAuth grant type", example="phone"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Successful auth login",
     *          @OA\JsonContent(
     *              @OA\Property(property="token_type", type="string"),
     *              @OA\Property(property="expires_in", type="integer"),
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="refresh_token", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors** Could not create token.",
     *     ),
     * )
     *
     * @OA\Post(
     *     path="/login-social",
     *     operationId="login-social",
     *     tags={"oAuth"},
     *     summary="Authentication by social",
     *     description="

### Example URI
**POST** https://your-website.com/api/v1/login-social",

     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                 required={"grant_type", "provider", "access_token"},
    *                  type="object",
    *                  @OA\Property(property="grant_type", type="string", description="oAuth grant type", example="social"),
    *                  @OA\Property(property="provider", type="string", description="google|apple|facebook", example="google"),
    *                  @OA\Property(property="access_token", type="string", description="Your social token"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Successful auth login",
     *          @OA\JsonContent(
     *              @OA\Property(property="token_type", type="string"),
     *              @OA\Property(property="expires_in", type="integer"),
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="refresh_token", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors** Could not create token.",
     *     ),
     * )
     *
     *
     * @param ServerRequestInterface $request
     * @return JsonResponse|Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        try {
            # get grant type
            $grantType = $request->getParsedBody()['grant_type'];

            if ($grantType == 'password') {
                # get username (default is :email)
                $email = Str::lower($request->getParsedBody()['username']);

                /** @var User|null $user */
                if ( is_null($user = User::query()->where('email', $email)->first()) ) {
                    return $this->baseController->sendOAuthError(
                        __('The user credentials were incorrect'),
                        'invalid_credentials',
                        Response::HTTP_UNAUTHORIZED
                    );
                }
                if (!$user->hasVerifiedEmail()) {
                    return $this->baseController->sendOAuthError(
                        __('Email address is not verified'),
                        'invalid_credentials',
                        Response::HTTP_UNAUTHORIZED
                    );
                }
            }

            if ($grantType == 'phone') {
                # get phone
                $phoneRequest = $request->getParsedBody()['phone'];

                # phone normalized
                $phone = (string) PhoneNumber::make($phoneRequest);

                /** @var User|null $user */
                if ( is_null($user = User::query()->where('phone', $phone)->first()) ) {
                    return $this->baseController->sendOAuthError(
                        __('The user credentials were incorrect'),
                        'invalid_credentials',
                        Response::HTTP_UNAUTHORIZED
                    );
                }
                if (!$user->hasVerifiedPhone()) {
                    return $this->baseController->sendOAuthError(
                        __('Phone is not verified'),
                        'invalid_credentials',
                        Response::HTTP_UNAUTHORIZED
                    );
                }
            }

            if ($grantType == 'social') {
                $providerUser = null;
                $provider = $request->getParsedBody()['provider'];

                $socialiteDriver = Socialite::driver($provider);
                $providerUser = $socialiteDriver->scopes(['name', 'email'])->userFromToken($request->getParsedBody()['access_token']);

                $userSocialAccount = UserSocialAccount::query()
                    ->where('provider_name', $provider)
                    ->where('provider_id', $providerUser->getId())
                    ->first();
            }

            # generate token
            $tokenResponse = parent::issueToken($request->withParsedBody(\Arr::collapse([
                [
                    "client_id" => config('passport.password_grant_client_user.id'),
                    "client_secret"=> config('passport.password_grant_client_user.secret')
                ],
                $request->getParsedBody()
            ])));

            # convert response to json string
            $content = $tokenResponse->getContent();

            # convert json to array
            $data = json_decode($content, true);

            if ($grantType == 'social') $userSocialAccount ? $data['registration'] = false : $data['registration'] = true;

            if (isset($data["error"])) {
                return $this->baseController->sendOAuthError(
                    __('The user credentials were incorrect'),
                    'invalid_credentials',
                    Response::HTTP_UNAUTHORIZED
                );
            }

            return response()->json($data, Response::HTTP_OK);

        } catch (Exception $exception) {
            return $this->baseController->sendOAuthError(
                $exception->getMessage(),
                OAuthServerException::invalidGrant()->getErrorType(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * Authorize a client to access the user's account.
     *
     * @OA\Post(
     *     path="/refresh-token",
     *     operationId="refresh-token",
     *     tags={"oAuth"},
     *     summary="Refresh access token",
     *     description="

### Example URI
**POST** https://your-website.com/api/v1/refresh-token",

     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"grant_type", "refresh_token"},
     *                  type="object",
     *                  @OA\Property(property="grant_type", type="string", description="oAuth grant type", example="refresh_token"),
     *                  @OA\Property(property="refresh_token", type="string", description="oAuth refresh_token"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Successful auth login",
     *          @OA\JsonContent(
     *              @OA\Property(property="token_type", type="string"),
     *              @OA\Property(property="expires_in", type="integer"),
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="refresh_token", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors** Could not create token.",
     *     ),
     * )
     *
     * @param ServerRequestInterface $request
     * @return JsonResponse|Response
     */
    public function refreshToken(ServerRequestInterface $request)
    {
        try {
            # generate token
            $tokenResponse = parent::issueToken($request->withParsedBody(\Arr::collapse([
                [
                    "client_id" => config('passport.password_grant_client_user.id'),
                    "client_secret"=> config('passport.password_grant_client_user.secret')
                ],
                $request->getParsedBody()
            ])));
            // $tokenResponse = parent::issueToken($request);

            # convert response to json string
            $content = $tokenResponse->getContent();

            # convert json to array
            $data = json_decode($content, true);

            if (isset($data["error"])) {
                return $this->baseController->sendOAuthError(
                    __('The user credentials were incorrect'),
                    'invalid_credentials',
                    Response::HTTP_UNAUTHORIZED
                );
            }

            return response()->json($data, Response::HTTP_OK);

        } catch (Exception $exception) {
            return $this->baseController->sendOAuthError(
                $exception->getMessage(),
                OAuthServerException::invalidGrant()->getErrorType(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/token",
     *     security={{ "passport": {"*"} }},
     *     operationId="check-token",
     *     tags={"oAuth"},
     *     summary="Check token status",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK**",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized**",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function checkTokenStatus()
    {
        if (!auth('api')->check()) {
            return $this->baseController->sendError('', Response::HTTP_UNAUTHORIZED);
        }

        return $this->baseController->sendResponse([], Response::HTTP_OK);
    }
}
