<?php

namespace App\Http\Controllers\Api\Admin;

use Exception;

use App\Http\Controllers\Api\ApiBaseController;

use Laravel\Passport\TokenRepository;
use Laravel\Passport\Http\Controllers\AccessTokenController;

use Illuminate\Http\Response;
use Illuminate\Support\Str;

use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

use App\Models\Admins\Admin;

/**
     * Authorize a client to access the user's account.
     *
     * @OA\Post(
     *     path="/admin/login",
     *     operationId="admin-login",
     *     tags={"Admin"},
     *     summary="Admin authentication",
     *     description="

### Example URI
**POST** https://your-website.com/api/v1/admin/login",

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
     * @param ServerRequestInterface $request
     * @return JsonResponse|Response
     */
class AdminLoginController extends AccessTokenController
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

    public function issueToken(ServerRequestInterface $request)
    {
        try {
            # get grant type
            $grantType = $request->getParsedBody()['grant_type'];

            if ($grantType == 'password') {
                # get username (default is :email)
                $email = Str::lower($request->getParsedBody()['username']);

                /** @var Admin|null $user */
                if ( is_null($user = Admin::query()->where('email', $email)->first()) ) {
                    return $this->baseController->sendOAuthError(
                        __('The user credentials were incorrect'),
                        'invalid_credentials',
                        Response::HTTP_UNAUTHORIZED
                    );
                }
            }

            # generate token
            $tokenResponse = parent::issueToken($request->withParsedBody(\Arr::collapse([
                [
                    "client_id" => config('passport.password_grant_client_admin.id'),
                    "client_secret"=> config('passport.password_grant_client_admin.secret')
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
     *     path="/admin/token",
     *     security={{ "passport": {"*"} }},
     *     operationId="check-admin-token",
     *     tags={"Admin"},
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
        if (!auth('api-admin')->check()) {
            return $this->baseController->sendError('', Response::HTTP_UNAUTHORIZED);
        }

        return $this->baseController->sendResponse([], '', Response::HTTP_OK);
    }
}
