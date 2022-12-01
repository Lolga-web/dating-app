<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace' => 'Auth',
], function () {

    // By email
    Route::group([
        'prefix' => 'email'
    ], function () {
        Route::post('send-code', [RegisterController::class, 'sendEmailCode']);
        Route::post('confirm-code', [RegisterController::class, 'confirmEmailCode'])->name('confirm-email-by-code');
        Route::post('register', [RegisterController::class, 'registerByEmail'])->name('email-register');
        Route::post('password-reset-send-code', [PasswordController::class, 'sendResetEmail']);
        Route::post('password-reset-by-code', [PasswordController::class, 'resetByEmailCode'])->name('reset-by-email-code');
    });

    // By phone
    Route::group([
        'prefix' => 'phone'
    ], function () {
        Route::post('send-code', [RegisterController::class, 'sendPhoneCode']);
        Route::post('confirm-code', [RegisterController::class, 'confirmPhoneCode'])->name('confirm-phone-by-code');
        Route::post('register', [RegisterController::class, 'registerByPhone'])->name('phone-register');
        Route::post('password-reset-send-code', [PasswordController::class, 'sendResetSms']);
        Route::post('password-reset-by-code', [PasswordController::class, 'resetBySmsCode'])->name('reset-by-sms-code');
    });

    Route::post('login-email', [LoginController::class, 'issueToken'])->name('login');
    Route::post('login-phone', [LoginController::class, 'issueToken']);
    Route::post('login-social', [LoginController::class, 'issueToken']);
    Route::post('refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('token', [LoginController::class, 'checkTokenStatus']);
    Route::get('logout', [LogoutController::class, 'logout'])->middleware('auth:api');
});
