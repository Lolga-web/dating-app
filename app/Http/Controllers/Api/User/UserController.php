<?php

namespace App\Http\Controllers\Api\User;

use Throwable;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;

use App\Http\Controllers\Api\ApiBaseController;

use App\Http\Requests\Api\Auth\ConfirmCodeRequest;
use App\Http\Requests\Api\Auth\EmailRequest;
use App\Http\Requests\Api\Auth\PhoneRequest;
use App\Http\Requests\Api\User\BlockedRequest;
use App\Http\Requests\Api\User\ResetPasswordRequest;
use App\Http\Requests\Api\User\UnlockRequest;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Requests\Api\User\UsersSearchRequest;

use App\Http\Resources\User\AuthUserResource;
use App\Http\Resources\User\IndexResource;
use App\Http\Resources\User\ShowResource;

use App\Mail\Auth\VerifyCodeMail;

use App\Models\Users\User;
use App\Models\Users\UserEmailVerify;
use App\Models\Users\UserPhoneVerify;

use App\Services\Users\UserService;
use App\Events\MyEvent;
use App\External\Twilio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Propaganistas\LaravelPhone\PhoneNumber;

class UserController extends ApiBaseController
{
    /** @var UserService */
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/user",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-auth-user",
     *     tags={"User"},
     *     summary="Get auth user data",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** User data",
     *          @OA\JsonContent(ref="#/components/schemas/AuthUserResource")
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors**",
     *     ),
     * )
     *
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function getAuthUser(): JsonResponse
    {
        return $this->sendResponse(AuthUserResource::make(\Auth::user()), __('User data'));
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     security={{ "passport": {"*"} }},
     *     operationId="users-list",
     *     tags={"User"},
     *     summary="Get user's list",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/UsersSearchRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Users list",
     *          @OA\JsonContent(ref="#/components/schemas/UserIndexResource")
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
     * @param UsersSearchRequest $request
     * @return JsonResponse
     */
    public function index(UsersSearchRequest $request): JsonResponse
    {
        $users = User::query()
                     ->role('user')
                     ->whereNotIn('id', [\Auth::id()])
                     ->online($request->input('online'))
                     ->when(!$request->input('filters.location'), function ($query) use ($request) {
                        return $query->location($request->input('location'));
                     })
                     ->when($request->input('filters.location'), function ($query) use ($request) {
                        return $query->whereHas('location', function (Builder $query) use ($request) {
                            $query->within(
                                            $request->input('filters.location.range'),
                                            'kilometers',
                                            $request->input('filters.location.lat'),
                                            $request->input('filters.location.lng')
                                        );
                        });
                     })
                     ->latest()
                     ->paginate(20);

        $users->whereNotNull('location')->map->addDistance(\Auth::user());

        return $this->sendResponse(IndexResource::collection($users), __('Users list'));
    }

    /**
     * @OA\Get(
     *     path="/users/{user}",
     *     security={{ "passport": {"*"} }},
     *     operationId="user-data",
     *     tags={"User"},
     *     summary="Get user data by ID",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer"), description="User id", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** User data",
     *          @OA\JsonContent(ref="#/components/schemas/UserShowResource")
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
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return $this->sendResponse(ShowResource::make($user), __('User data'));
    }

    /**
     * @OA\Put(
     *     path="/user",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-user-data",
     *     tags={"User"},
     *     summary="Update auth user data",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** User data successfully updated",
     *          @OA\JsonContent(ref="#/components/schemas/AuthUserResource")
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
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $user = \Auth::user();
        $user->update($request->validated());

        return $this->sendResponse(AuthUserResource::make($user), __('User data successfully updated'));
    }

     /**
     * @OA\Delete(
     *     path="/user",
     *     security={{ "passport": {"*"} }},
     *     operationId="delete-user",
     *     tags={"User"},
     *     summary="Delete user",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Profile deleted",
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
    public function destroy(): JsonResponse
    {
        \Auth::user()->delete();

        return $this->sendResponse([], __('Profile deleted'));
    }

    /**
     * @OA\Post(
     *     path="/user/email-code",
     *     security={{ "passport": {"*"} }},
     *     operationId="send-user-email-verification-code",
     *     tags={"User"},
     *     summary="Send email verification code for auth user",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EmailRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Verification code sent successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="timer", type="integer", description="seconds", example=300),
     *          )
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors**",
     *     ),
     * )
     *
     * @param EmailRequest $request
     * @return JsonResponse
     */
    public function sendEmailCode(EmailRequest $request): JsonResponse
    {
        $userEmailVerify = UserEmailVerify::updateOrCreate([
            'email' => $request->getEmail(),
        ], [
            'user_id' => \Auth::id(),
            'code' => $this->userService->generateConfirmCode(),
            'confirmed' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($userEmailVerify->email)->locale(app()->getLocale())->queue(new VerifyCodeMail($userEmailVerify));

        return $this->sendResponse([
            'timer' => 300,
        ], __('Verification code sent successfully'));
    }

    /**
     * @OA\Put(
     *     path="/user/email",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-user-email",
     *     tags={"User"},
     *     summary="Update auth user email",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EmailConfirmCodeRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Email updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/AuthUserResource")
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
     * @param ConfirmCodeRequest $request
     * @return JsonResponse
     */
    public function updateEmail(ConfirmCodeRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $userEmailVerify = UserEmailVerify::query()
                ->getByValue($request->getEmail())
                ->delete();

            $user = \Auth::user();
            $user->update(['email' => $request->input('email')]);
            $user->markEmailAsVerified();

            DB::commit();

            return $this->sendResponse(AuthUserResource::make($user), __('Email updated successfully'));

        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/phone-code",
     *     operationId="send-user-phone-verification-code",
     *     tags={"User"},
     *     summary="Send phone verification code for auth user",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/PhoneRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Verification code sent successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="timer", type="integer", description="seconds", example=300),
     *          )
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors**",
     *     ),
     * )
     *
     * @param PhoneRequest $request
     * @return JsonResponse
     */
    public function sendPhoneCode(PhoneRequest $request): JsonResponse
    {
        $phone = (string) PhoneNumber::make($request->get('phone'));

        $userPhoneVerify = UserPhoneVerify::updateOrCreate([
            'phone' => $phone,
        ], [
            'user_id' => \Auth::id(),
            // 'code' => app()->isProduction() ? $this->userService->generateConfirmCode() : '111111',
            'code' => $this->userService->generateConfirmCode(),
            'confirmed' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        // if (app()->isProduction()) {
            (new Twilio)->sendSms($phone, __('Your confirm code in ***: ') . $userPhoneVerify->code);
        // };

        return $this->sendResponse([
            'timer' => 300,
        ], __('Verification code sent successfully'));
    }

    /**
     * @OA\Put(
     *     path="/user/phone",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-user-phone",
     *     tags={"User"},
     *     summary="Update auth user phone",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/PhoneConfirmCodeRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Phone updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/AuthUserResource")
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @param ConfirmCodeRequest $request
     * @return JsonResponse
     */
    public function updatePhone(ConfirmCodeRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $userPhoneVerify = UserPhoneVerify::query()
                ->getByValue($request->input('phone'))
                ->delete();

            $user = \Auth::user();
            $user->update(['phone' => $request->input('phone')]);
            $user->markPhoneAsVerified();

            DB::commit();

            return $this->sendResponse(AuthUserResource::make($user), __('Phone updated successfully'));

        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Put(
     *     path="/user/password",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-user-password",
     *     tags={"User"},
     *     summary="Update auth user password",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Password updated successfully",
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     * )
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(ResetPasswordRequest $request): JsonResponse
    {
        \Auth::user()->update(['password' => $request->get('password')]);

        return $this->sendResponse([], __("Password updated successfully"));
    }

    /**
     * @OA\Post(
     *     path="/block-user/{user}",
     *     security={{ "passport": {"*"} }},
     *     operationId="block-user",
     *     tags={"User"},
     *     summary="Block user",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer"), description="User id", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** User blocked successfully",
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
     * @param BlockedRequest $request
     * @return JsonResponse
     */
    public function blockUser(User $user, BlockedRequest $request): JsonResponse
    {
        \Auth::user()->blockedUsers()->save($user);

        return $this->sendResponse([], __('User blocked successfully'));
    }

    /**
     * @OA\Post(
     *     path="/unlock-user/{user}",
     *     security={{ "passport": {"*"} }},
     *     operationId="unlock-user",
     *     tags={"User"},
     *     summary="Unlock user",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer"), description="User id", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** User unblocked successfully",
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
     * @param BlockedRequest $request
     * @return JsonResponse
     */
    public function unlockUser(User $user, UnlockRequest $request): JsonResponse
    {
        \Auth::user()->blockedUsers()->detach($user);

        return $this->sendResponse([], __('User unblocked successfully'));
    }

    /**
     * @OA\Get(
     *     path="/blocked-users",
     *     security={{ "passport": {"*"} }},
     *     operationId="blocked-users",
     *     tags={"User"},
     *     summary="Get blocked users list",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Blocked users list",
     *          @OA\JsonContent(ref="#/components/schemas/UserIndexResource")
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
     * @param UsersSearchRequest $request
     * @return JsonResponse
     */
    public function locks(): JsonResponse
    {
        $locks = \Auth::user()->blockedUsers()->get();

        return $this->sendResponse(IndexResource::collection($locks), __('Blocked users list'));
    }

    public function testWebsockets(string $text="hello world")
    {
        event(new MyEvent($text));
    }

    /**
     * @OA\Post(
     *     path="/test-push",
     *     security={{ "passport": {"*"} }},
     *     operationId="test-push",
     *     tags={"Test"},
     *     summary="Test push notification",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"title", "body", "event_name"},
     *                  type="object",
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="body", type="string"),
     *                  @OA\Property(property="type", type="string"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="**OK*",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", example="true"),
     *              @OA\Property(property="answer", description="ответ firebase", example="projects/best-date-3c55e/messages/1823595210671006799"),
     *              @OA\Property(property="message", description="то что отправлено в firebase"),
     *          )
     *     ),
     *      @OA\Response(
     *          response="422",
     *          description="**HTTP_UNPROCESSABLE_ENTITY*",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", example="false"),
     *              @OA\Property(property="answer", description="ответ firebase", example="The registration token is not a valid FCM registration token."),
     *              @OA\Property(property="message", description="то что отправлено в firebase"),
     *          )
     *     ),
     * )
     * */
    public function testPush(Request $request)
    {
        $token = \Auth::user()->devices()->first();

        if (!$token) return new JsonResponse([
            'success' => false,
            'answer' => 'No token for user',
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $validatedData = $request->validate([
            'title' => ['required', 'string'],
            'body' => ['required', 'string'],
            'type' => ['required', 'string'],
            'another' => ['string'],
        ]);

        $notification = Notification::fromArray([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'image' => asset('images/email/logo_pink.png'),
        ]);

        $data = [
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'message_text' => $request->input('body'),
            'resource' => $request->input('another'),
            'user' => \Auth::user(),
        ];

        $message = CloudMessage::withTarget('token', \Auth::user()->devices()->first()->token)
                                ->withNotification($notification)
                                ->withData($data)
                                ->withApnsConfig(
                                    ApnsConfig::fromArray([
                                        'headers' => [
                                            'apns-priority' => '10',
                                        ],
                                        'payload' => [
                                            'aps' => [
                                                'alert' => [
                                                    'title' => $request->input('title'),
                                                    'body' => $request->input('body'),
                                                ],
                                                'badge' => 42,
                                                'sound' => 'default',
                                            ],
                                        ],
                                    ])
                                )
                                ->withAndroidConfig(
                                    AndroidConfig::fromArray([
                                        'ttl' => '3600s',
                                        'priority' => 'normal',
                                        'notification' => [
                                            'title' => $request->input('title'),
                                            'body' => $request->input('body'),
                                            'icon' => asset('images/email/logo_pink.png'),
                                            'color' => '#f45342',
                                            'sound' => 'default',
                                        ],
                                    ])
                                );

                                try {
                                    $answer = Firebase::messaging()->send($message);
                                    $message = [
                                        'success' => true,
                                        'answer' => $answer,
                                        'message' => $message,
                                    ];
                                    $httpStatusCode = JsonResponse::HTTP_OK;
                                } catch (Throwable $e) {
                                    Log::debug($e->getMessage());
                                    $message = [
                                        'success' => false,
                                        'answer' => 'The registration token is not a valid FCM registration token.',
                                        'message' => $message,
                                    ];
                                    $httpStatusCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;
                                }

            return new JsonResponse($message, $httpStatusCode);
    }

}
