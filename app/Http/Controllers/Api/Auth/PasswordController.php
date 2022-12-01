<?php

namespace App\Http\Controllers\Api\Auth;

use App\External\Twilio;
use Throwable;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Api\ApiBaseController;

use App\Http\Requests\Api\Auth\ConfirmResetRequest;
use App\Http\Requests\Api\Auth\SendResetEmailRequest;
use App\Http\Requests\Api\Auth\SendResetPhoneRequest;
use App\Mail\Auth\ResetPasswordCodeMail;
use App\Mail\Auth\ResetSuccessMail;
use App\Models\PasswordResets;

use App\Services\Users\UserService;
use Propaganistas\LaravelPhone\PhoneNumber;

class PasswordController extends ApiBaseController
{
    /** @var UserService $userService*/
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="/email/password-reset-send-code",
     *     operationId="email-password-reset-send-code",
     *     tags={"oAuth"},
     *     summary="Send email with code for password reset",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/SendResetEmailRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Verification code sent successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="timer", type="integer", description="seconds", example=300),
     *          )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="**Unprocessable Entity** Required fields are missing or cannot be processed."
     *     )
     * )
     *
     * @param SendResetEmailRequest $request
     *
     * @return JsonResponse
     */
    public function sendResetEmail(SendResetEmailRequest $request): JsonResponse
    {
        /** @var PasswordResets $passwordReset */
        $passwordReset = PasswordResets::updateOrCreate([
            'email' => $request->getEmail()
        ], [
            'code' => $this->userService->generateConfirmCode(),
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($request->getEmail())->locale(app()->getLocale())->queue(new ResetPasswordCodeMail($passwordReset));

        return $this->sendResponse([
            'timer' => 300,
        ], __('Verification code sent successfully'));
    }

    /**
     * @OA\Post(
     *     path="/email/password-reset-by-code",
     *     operationId="email-password-reset-by-code",
     *     tags={"oAuth"},
     *     summary="Reset password by email code",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ConfirmResetRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Reset successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="**Unprocessable Entity** Required fields are missing or cannot be processed."
     *     )
     * )
     *
     * @param ConfirmResetRequest $request
     *
     * @return JsonResponse
     */
    public function resetByEmailCode(ConfirmResetRequest $request): JsonResponse
    {
        /** @var PasswordResets $passwordReset */
        $passwordReset = PasswordResets::where([
            'email' => $request->getEmail(),
            'code' => $request->getConfirmCode()
        ])->first();

        $user = $this->userService->findByEmail($request->getEmail());

        try {
            DB::beginTransaction();
                $user->resetPassword($request->input('password'));
                $user->passwordResets()->delete();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        Mail::to($user->email)->locale(app()->getLocale())->queue(new ResetSuccessMail($user));

        return $this->sendResponse([], __('Reset successfully'));
    }

    /**
     * @OA\Post(
     *     path="/phone/password-reset-send-code",
     *     operationId="phone-password-reset-send-code",
     *     tags={"oAuth"},
     *     summary="Send sms with code for password reset",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/SendResetPhoneRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Verification code sent successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="timer", type="integer", description="seconds", example=300),
     *          )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="**Unprocessable Entity** Required fields are missing or cannot be processed."
     *     )
     * )
     *
     * @param SendResetPhoneRequest $request
     *
     * @return JsonResponse
     */
    public function sendResetSms(SendResetPhoneRequest $request): JsonResponse
    {
        $phone = (string) PhoneNumber::make($request->get('phone'));

        /** @var PasswordResets $passwordReset */
        $passwordReset = PasswordResets::updateOrCreate([
            'phone' => $phone,
        ], [
            // 'code' => app()->isProduction() ? $this->userService->generateConfirmCode() : '111111',
            'code' => $this->userService->generateConfirmCode(),
            'expires_at' => now()->addMinutes(5),
        ]);

        // if (app()->isProduction()) {
            (new Twilio)->sendSms($phone, __('Your confirm code in ***: ') . $passwordReset->code);
        // };

        return $this->sendResponse([
            'timer' => 300,
        ], __('Verification code sent successfully'));
    }

    /**
     * @OA\Post(
     *     path="/phone/password-reset-by-code",
     *     operationId="phone-password-reset-by-code",
     *     tags={"oAuth"},
     *     summary="Reset password by phone code",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ConfirmResetRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Reset successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="**Unprocessable Entity** Required fields are missing or cannot be processed."
     *     )
     * )
     *
     * @param ConfirmResetRequest $request
     *
     * @return JsonResponse
     */
    public function resetBySmsCode(ConfirmResetRequest $request): JsonResponse
    {
        /** @var PasswordResets $passwordReset */
        $passwordReset = PasswordResets::where([
            'phone' => $request->input('phone'),
            'code' => $request->getConfirmCode()
        ])->first();

        $user = $this->userService->findByPhone($request->input('phone'));

        try {
            DB::beginTransaction();
                $user->resetPassword($request->input('password'));
                $user->passwordResets()->delete();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        Mail::to($user->email)->locale(app()->getLocale())->queue(new ResetSuccessMail($user));

        return $this->sendResponse([], __('Reset successfully'));
    }
}
