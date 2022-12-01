<?php

use App\Http\Controllers\Api\User\GuestController;
use App\Http\Controllers\Api\User\PhotoController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\VotingController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\User\DeviceController;
use App\Http\Controllers\Api\User\LocationController;
use App\Http\Controllers\Api\User\QuestionnaireController;
use App\Http\Controllers\Api\User\SettingsController;
use Illuminate\Support\Facades\Route;

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
    'namespace' => 'User',
    'middleware' => ['auth:api', 'last.online'],
], function () {
    Route::group([
        'prefix' => 'user',
    ], function () {
        Route::get('/', [UserController::class, 'getAuthUser']);
        Route::put('/', [UserController::class, 'update']);
        Route::delete('/', [UserController::class, 'destroy']);
        Route::post('email-code', [UserController::class, 'sendEmailCode']);
        Route::put('email', [UserController::class, 'updateEmail'])->name('update-email');
        Route::post('phone-code', [UserController::class, 'sendPhoneCode']);
        Route::put('phone', [UserController::class, 'updatePhone'])->name('update-phone');
        Route::put('password', [UserController::class, 'updatePassword'])->name('update-password');

        Route::put('location', [LocationController::class, 'update']);

        Route::put('/questionnaire', [QuestionnaireController::class, 'update']);

        Route::group([
            'prefix' => 'photos',
        ], function () {
            Route::post('/', [PhotoController::class, 'store']);
            Route::put('/{photo}', [PhotoController::class, 'update']);
            Route::delete('/{photo}', [PhotoController::class, 'destroy']);
            Route::delete('/', [PhotoController::class, 'destroyArray']);
        });
    });

    Route::post('device-token', [DeviceController::class, 'store']);

    Route::post('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show'])->middleware('guest');

    Route::get('blocked-users', [UserController::class, 'locks']);
    Route::post('block-user/{user}', [UserController::class, 'blockUser']);
    Route::post('unlock-user/{user}', [UserController::class, 'unlockUser']);

    Route::resource('likes' , 'LikeController')->only(['index', 'store']);

    Route::post('voting-photos', [VotingController::class, 'getPhotos']);
    Route::post('top', [VotingController::class, 'index']);
    Route::get('voting', [VotingController::class, 'show'])->middleware('viewed.voting');
    Route::post('voting', [VotingController::class, 'store']);

    Route::get('guests', [GuestController::class, 'index']);
    Route::put('guests', [GuestController::class, 'update']);

    Route::group([
        'prefix' => 'settings',
    ], function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::put('/', [SettingsController::class, 'update']);
        Route::put('/language', [SettingsController::class, 'updateLanguage']);
    });
});

Route::group([
    'middleware' => ['auth:api', 'last.online'],
], function () {
    Route::get('chats', [ChatController::class, 'getChatsList']);
    Route::get('chat/{user}', [ChatController::class, 'showChat'])->middleware('read.message');
    Route::delete('chat/{user}', [ChatController::class, 'deleteChat']);
    Route::post('chat-typing-event/{recipient}', [ChatController::class, 'sendChatTypingEvent']);
    Route::post('chat-read-event/{recipient}', [ChatController::class, 'sendChatReadEvent']);
    Route::post('message/{recipient}/{parent?}', [ChatController::class, 'store']);
    Route::put('message/{message}', [ChatController::class, 'update']);
    Route::delete('message/{message}', [ChatController::class, 'delete']);

    Route::resource('invitations' , 'InvitationController')->only(['index', 'store', 'update']);
    Route::get('invitation-answers', [InvitationController::class, 'getInvitationAnswers']);
    Route::post('user/invitations', [InvitationController::class, 'getUserInvitations']);
});

Route::group([
    'middleware' => ['auth:api', 'last.online'],
], function () {
    Route::get('match-users', [MatchController::class, 'getUsers']);
    Route::get('match', [MatchController::class, 'index'])->middleware('viewed.match');
    Route::post('match', [MatchController::class, 'store']);
});

Route::post('test-websockets/{text?}', [UserController::class, 'testWebsockets']);
Route::post('test-push', [UserController::class, 'testPush'])->middleware('auth:api');


