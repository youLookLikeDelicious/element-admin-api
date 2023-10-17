<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\System\MenuController;
use App\Http\Controllers\System\RoleController;
use App\Http\Controllers\System\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/current-user', [LoginController::class, 'currentUser']);  // get current user
    Route::resource('menu', MenuController::class);                         // Menu resource
    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
});

Route::group([], function () {
    Route::post('/login', [LoginController::class, 'login'])->middleware('captcha:login');
    Route::get('/captcha/{config?}', [IndexController::class, 'captcha']);      // 验证码
});