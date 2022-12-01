<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiBaseController;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LogoutController extends ApiBaseController
{
    /**
     * Logout user and invalidate token.
     *
     * @OA\Get(
     *     path="/logout",
     *     security={{ "passport": {"*"} }},
     *     operationId="logout",
     *     tags={"oAuth"},
     *     summary="Logout and Token Invalidation",
     *     description="

Sending an request to logout endpoint with a valid API token will also invalidate that token.
### Example URI
**GET** https://your-website.com/api/v1/logout",

     *     @OA\Response(
     *          response="200",
     *          description="**OK** You are successfully logged out",
     *          @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * Logout admin and invalidate token.
     *
     * @OA\Get(
     *     path="/admin/logout",
     *     security={{ "passport": {"*"} }},
     *     operationId="admin-logout",
     *     tags={"Admin"},
     *     summary="Logout and Token Invalidation",
     *     description="

Sending an request to logout endpoint with a valid API token will also invalidate that token.
### Example URI
**GET** https://your-website.com/api/v1/admin/logout",

     *     @OA\Response(
     *          response="200",
     *          description="**OK** You are successfully logged out",
     *          @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        $request->user()->devices()->delete();

        return $this->sendResponse([], __('You are successfully logged out'));
    }
}
