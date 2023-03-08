<?php

use App\Http\Controllers\DisableUserController;
use App\Http\Controllers\EnableUserController;
use App\Http\Controllers\ListUsersController;
use App\Http\Controllers\RegisterUserController;
use Illuminate\Routing\Router;
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

Route::prefix('v1')->group(function (Router $router) {
    $router->get('/users', ListUsersController::class);
    $router->post('/users', RegisterUserController::class);
    $router->patch('/users/{user}/enable', EnableUserController::class);
    $router->patch('/users/{user}/disable', DisableUserController::class);
});
