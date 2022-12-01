<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Api\User\QuestionnaireRequest;
use App\Http\Resources\User\AuthUserResource;

class QuestionnaireController extends ApiBaseController
{
    /**
     * @OA\Put(
     *     path="/user/questionnaire",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-questionnaire",
     *     tags={"User"},
     *     summary="Update questionnaire",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/QuestionnaireRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Questionnaire successfully updated",
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
    public function update(QuestionnaireRequest $request)
    {
        $user = \Auth::user();

        $user->questionnaire()->update($request->all());

        return $this->sendResponse(AuthUserResource::make($user), __('Questionnaire successfully updated'));
    }
}
