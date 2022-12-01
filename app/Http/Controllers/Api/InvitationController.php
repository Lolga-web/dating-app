<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Invitation\IndexRequest;
use App\Http\Requests\Api\Invitation\StoreRequest;
use App\Http\Requests\Api\Invitation\UpdateRequest;

use App\Http\Resources\Invitations\InvitationResource;
use App\Http\Resources\Invitations\UserInvitationResource;

use App\Models\Invitations\Invitation;
use App\Models\Invitations\InvitationAnswer;
use App\Models\Invitations\UserInvitations;
use App\Models\Users\User;

use function App\send_push_notify;

class InvitationController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/invitations",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-invitations",
     *     tags={"Invitations"},
     *     summary="Get invitations list",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Invitations list",
     *          @OA\JsonContent(ref="#/components/schemas/InvitationResource")
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
        $invitations = Invitation::all();

        return $this->sendResponse(InvitationResource::collection($invitations), __('Invitations list'));
    }

    /**
     * @OA\Post(
     *     path="/invitations",
     *     security={{ "passport": {"*"} }},
     *     operationId="store-invitation",
     *     tags={"Invitations"},
     *     summary="Send invitation",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/InvitationStoreRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Invitation send successfully",
     *          @OA\JsonContent(ref="#/components/schemas/UserInvitationResource")
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
    public function store(StoreRequest $request)
    {
        $invitation = UserInvitations::create([
            'invitation_id' => $request->input('invitation_id'),
            'from_user_id' => \Auth::id(),
            'to_user_id' => $request->input('user_id'),
        ]);

        $user = User::find($request->input('user_id'));
        if ($user->canNotify('invitation')) {
            send_push_notify(
                'invitation',
                __('New invitation'),
                __('New invitation from ') . \Auth::user()->name,
                $user
            );
        }

        return $this->sendResponse(UserInvitationResource::make($invitation), __('Invitation send successfully'));
    }

    /**
     * @OA\Put(
     *     path="/invitations/{invitation}",
     *     security={{ "passport": {"*"} }},
     *     operationId="add-invitation-answer",
     *     tags={"Invitations"},
     *     summary="Answer to invitation",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="invitation", in="path", required=true, @OA\Schema(type="string"), description="invitation ID", example=1),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/InvitationUpdateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Answer send successfully",
     *          @OA\JsonContent(ref="#/components/schemas/UserInvitationResource")
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
    public function update(UserInvitations $invitation, UpdateRequest $request)
    {
        $invitation->update(['answer_id' => $request->input('answer_id')]);

        return $this->sendResponse(UserInvitationResource::make($invitation), __('Answer send successfully'));
    }

    /**
     * @OA\Post(
     *     path="/user/invitations",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-user-invitations",
     *     tags={"Invitations"},
     *     summary="Get user invitations list",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/InvitationIndexRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Invitations list",
     *          @OA\JsonContent(ref="#/components/schemas/UserInvitationResource")
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
    public function getUserInvitations(IndexRequest $request)
    {
        $invitations = \Auth::user()->getInvitations($request->input('filter'))
                                    ->latest()
                                    ->paginate(20);

        return $this->sendResponse(UserInvitationResource::collection($invitations), __('Invitations list'));
    }

    /**
     * @OA\Get(
     *     path="/invitation-answers",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-invitation-answers",
     *     tags={"Invitations"},
     *     summary="Get invitation answers list",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Invitation answers list",
     *          @OA\JsonContent(ref="#/components/schemas/InvitationAnswerResource")
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
    public function getInvitationAnswers()
    {
        $answers = InvitationAnswer::all();

        return $this->sendResponse(InvitationResource::collection($answers), __('Invitation answers list'));
    }
}
