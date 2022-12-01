<?php

namespace App\Http\Controllers\Api\Auth;

use App\External\Twilio;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Verified;
use Propaganistas\LaravelPhone\PhoneNumber;

use App\Http\Controllers\Api\ApiBaseController;

use App\Http\Requests\Api\Auth\ConfirmCodeRequest;
use App\Http\Requests\Api\Auth\EmailRequest;
use App\Http\Requests\Api\Auth\PhoneRequest;
use App\Http\Requests\Api\Auth\RegistrationRequest;
use App\Http\Resources\User\AuthUserResource;
use App\Models\Users\UserEmailVerify;
use App\Models\Users\User;

use App\Mail\Auth\VerifyCodeMail;
use App\Mail\Auth\WelcomeMail;
use App\Models\Users\UserPhoneVerify;
use App\Services\Users\UserService;

use Throwable;

class RegisterController extends ApiBaseController
{
    /** @var UserService */
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="/email/send-code",
     *     operationId="send-email-verification-code",
     *     tags={"oAuth"},
     *     summary="Send email verification code",
     *     description="

    ### Example URI
     **POST** https://your-website.com/api/v1/email/send-code",
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
     *
     * @param EmailRequest $request
     *
     * @return JsonResponse
     */
    public function sendEmailCode(EmailRequest $request): JsonResponse
    {
        $userEmailVerify = UserEmailVerify::updateOrCreate([
            'email' => $request->getEmail()
        ], [
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
     * @OA\Post(
     *     path="/email/confirm-code",
     *     operationId="confirm-email-verification-code",
     *     tags={"oAuth"},
     *     summary="Confirm email verification code",
     *     description="

    ### Example URI
     **POST** https://your-website.com/api/v1/email/confirm-code",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EmailConfirmCodeRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Successful verification",
     *          @OA\JsonContent(ref="#/components/schemas/Response")
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
     * @param ConfirmCodeRequest $request
     *
     * @return JsonResponse
     */
    public function confirmEmailCode(ConfirmCodeRequest $request): JsonResponse
    {
        $userEmailVerify = UserEmailVerify::query()
            ->getByValue($request->getEmail())
            ->first();

        $userEmailVerify->markVerified();

        return $this->sendResponse([], __('Email successfully confirmed'));
    }

    /**
     * @OA\Post(
     *     path="/email/register",
     *     operationId="register-by-email",
     *     tags={"oAuth"},
     *     summary="User registration by email",
     *     description="

    ### Example URI
     **POST** https://your-website.com/api/v1/email/register",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/RegistrationRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** You were successfully registered. Use your email and password to sign in.",
     *          @OA\JsonContent(ref="#/components/schemas/AuthUserResource")
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
     *
     * @param RegistrationRequest $request
     *
     * @return JsonResponse
     */
    public function registerByEmail(RegistrationRequest $request): JsonResponse
    {
        $userEmailVerify = UserEmailVerify::query()
                                            ->getByValue($request->getEmail())
                                            ->confirmed()
                                            ->first();

        try {
            DB::beginTransaction();

            $user = new User($request->all());
            $userEmailVerify->user()->associate($user)->save();
            $userEmailVerify->delete();

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            Mail::to($user->email)->locale(app()->getLocale())->queue(new WelcomeMail());

            DB::commit();

            return $this->sendResponse(
                AuthUserResource::make($user),
                __('You were successfully registered. Use your email and password to sign in.')
            );
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Post(
     *     path="/phone/send-code",
     *     operationId="send-phone-verification-code",
     *     tags={"oAuth"},
     *     summary="Send phone verification code",
     *     description="

    ### Example URI
     **POST** https://your-website.com/api/v1/phone/send-code",
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
     *
     * @param PhoneRequest $request
     *
     * @return JsonResponse
     */
    public function sendPhoneCode(PhoneRequest $request)
    {
        $phone = (string) PhoneNumber::make($request->get('phone'));

        $userPhoneVerify = UserPhoneVerify::updateOrCreate([
            'phone' => $phone,
        ], [
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
     * @OA\Post(
     *     path="/phone/confirm-code",
     *     operationId="confirm-phone-verification-code",
     *     tags={"oAuth"},
     *     summary="Confirm phone verification code",
     *     description="

    ### Example URI
     **POST** https://your-website.com/api/v1/phone/confirm-code",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/PhoneConfirmCodeRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Successful verification",
     *          @OA\JsonContent(ref="#/components/schemas/Response")
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
     * @param ConfirmCodeRequest $request
     *
     * @return JsonResponse
     */
    public function confirmPhoneCode(ConfirmCodeRequest $request)
    {
        $userPhoneVerify = UserPhoneVerify::query()
        ->getByValue($request->input('phone'))
        ->first();

        $userPhoneVerify->markVerified();

        return $this->sendResponse([], __('Phone successfully confirmed'));
    }

    /**
     * @OA\Post(
     *     path="/phone/register",
     *     operationId="register-by-phone",
     *     tags={"oAuth"},
     *     summary="User registration by phone",
     *     description="

    ### Example URI
     **POST** https://your-website.com/api/v1/phone/register",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/RegistrationRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** You were successfully registered. Use your phone and password to sign in.",
     *          @OA\JsonContent(ref="#/components/schemas/AuthUserResource")
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
     *
     * @param RegistrationRequest $request
     *
     * @return JsonResponse
     */
    public function registerByPhone(RegistrationRequest $request)
    {
        $userPhoneVerify = UserPhoneVerify::query()
                                            ->getByValue($request->input('phone'))
                                            ->confirmed()
                                            ->first();

        try {
            DB::beginTransaction();

            $user = new User($request->all());
            $userPhoneVerify->user()->associate($user)->save();
            $userPhoneVerify->delete();

            $user->markPhoneAsVerified();

            Mail::to($user->email)->locale(app()->getLocale())->queue(new WelcomeMail());

            DB::commit();

            return $this->sendResponse(AuthUserResource::make($user), __('You were successfully registered. Use your phone and password to sign in.'));
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
