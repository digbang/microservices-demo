<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListUsersController;
use App\Http\Controllers\EnableUserController;
use App\Http\Controllers\DisableUserController;
use App\Http\Controllers\RegisterUserController;

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

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
        Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
        Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
    });
});

Route::prefix('v1')->group(function (Router $router) {
    $router->get('/users', ListUsersController::class);
    $router->post('/users', RegisterUserController::class);
    $router->patch('/users/{user}/enable', EnableUserController::class);
    $router->patch('/users/{user}/disable', DisableUserController::class);
});
