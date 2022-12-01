<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiBaseController;

use App\Http\Requests\Api\User\DeviceRequest;
use App\Models\Users\UserDevices;

/**
     * @OA\Post(
     *     path="/device-token",
     *     security={{ "passport": {"*"} }},
     *     operationId="store-device-token",
     *     tags={"User"},
     *     summary="Store device token",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/DeviceRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Device token save successfully"
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
class DeviceController extends ApiBaseController
{
    public function store(DeviceRequest $request)
    {
        UserDevices::updateOrCreate([
            'user_id' => \Auth::id()
        ], [
            'type' => $request->input('type'),
            'token' => $request->input('token')
        ]);

        return $this->sendResponse([], __('Device token save successfully'));
    }
}
