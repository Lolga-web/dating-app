<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiBaseController;

use App\Http\Requests\Api\User\SettingsRequest;
use App\Http\Resources\User\AuthUserResource;
use App\Http\Resources\User\SettingsResource;

class SettingsController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/settings",
     *     security={{ "passport": {"*"} }},
     *     operationId="user-settings",
     *     tags={"Settings"},
     *     summary="User settings",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Settings updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/SettingsResource")
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
    public function index()
    {
        return $this->sendResponse(SettingsResource::make(\Auth::user()->settings), __('User settings'));
    }

    /**
     * @OA\Put(
     *     path="/settings",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-settings",
     *     tags={"Settings"},
     *     summary="Update settings",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/SettingsRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Settings updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/SettingsResource")
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
     * @param SettingsRequest $request
     * @return JsonResponse
     */
    public function update(SettingsRequest $request)
    {
        \Auth::user()->settings()->update($request->validated());

        return $this->sendResponse(SettingsResource::make(\Auth::user()->settings), __('Settings updated successfully'));
    }

    /**
     * @OA\Put(
     *     path="/settings/language",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-user-language",
     *     tags={"Settings"},
     *     summary="Update user language",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/SettingsLanguageRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Settings updated successfully",
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
     * @param SettingsRequest $request
     * @return JsonResponse
     */
    public function updateLanguage(SettingsRequest $request)
    {
        \Auth::user()->update(['language' => $request->input('language')]);

        return $this->sendResponse(AuthUserResource::make(\Auth::user()), __('Settings updated successfully'));
    }

}
