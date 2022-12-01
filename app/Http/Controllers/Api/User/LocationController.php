<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiBaseController;

use App\Http\Requests\Api\User\LocationRequest;

use App\Http\Resources\User\AuthUserResource;

use App\Services\Users\UserService;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class LocationController extends ApiBaseController
{
    /** @var UserService $userService */
    private UserService $userService;

    /**
     * UserDataController constructor.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Put(
     *     path="/user/location",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-location",
     *     tags={"User"},
     *     summary="Update location",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/LocationRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Location successfully updated",
     *          @OA\JsonContent(ref="#/components/schemas/AuthUserResource")
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function update(LocationRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->userService->setLocation($request, \Auth::user());

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::debug($e->getMessage());

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse(AuthUserResource::make(\Auth::user()), __('Location successfully updated'));
    }
}
