<?php

use App\Http\Controllers\Api\Admin\AdminLoginController;
use App\Http\Controllers\Api\Admin\AdminUsersController;
use App\Http\Controllers\Api\Auth\LogoutController;
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
    'namespace' => 'Admin',
], function () {
    Route::group([
        'prefix' => 'admin',
        'middleware' => ['auth:api-admin'],
    ], function () {
        //auth
        Route::post('login', [AdminLoginController::class, 'issueToken'])->name('login-admin')->withoutMiddleware('auth:api-admin');
        Route::get('logout', [LogoutController::class, 'logout']);
        Route::post('token', [AdminLoginController::class, 'checkTokenStatus']);

        Route::resource('users', 'AdminUsersController', ['as' => 'admin'])->only([
            'index', 'show', 'store', 'update'
        ]);
        Route::delete('users', [AdminUsersController::class, 'destroy']);
    });
});
