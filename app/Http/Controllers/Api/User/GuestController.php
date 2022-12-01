<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\JsonResponse;

use App\Http\Controllers\Api\ApiBaseController;

use App\Http\Requests\Api\User\GuestViewedRequest;

use App\Http\Resources\User\GuestResource;

use App\Models\Guest;

class GuestController extends ApiBaseController
{
     /**
     * @OA\Get(
     *     path="/guests",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-user-guests",
     *     tags={"Guests"},
     *     summary="Get auth user guests",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Guest list",
     *          @OA\JsonContent(ref="#/components/schemas/GuestResource")
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
    public function index(): JsonResponse
    {
        $guests = \Auth::user()->guests()
                               ->where('visit_at', '>', now()->subMonth())
                               ->orderBy('visit_at', 'desc')
                               ->paginate(20);

        return $this->sendResponse(GuestResource::collection($guests), __('Guest list'));
    }

    /**
     * @OA\Put(
     *     path="/guests",
     *     security={{ "passport": {"*"} }},
     *     operationId="viewed-guests",
     *     tags={"Guests"},
     *     summary="Viewed guests",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/GuestViewedRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Guest list viewed",
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
    public function update(GuestViewedRequest $request): JsonResponse
    {
        Guest::whereIn('id', $request->input('ids'))->update(['viewed' => true]);

        return $this->sendResponse([], __('Guest list viewed'));
    }
}
