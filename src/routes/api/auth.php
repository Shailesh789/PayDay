<?php

use App\Http\Controllers\Api\Auth\AuthenticationController;
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

Route::post('login',[AuthenticationController::class,'login']);
Route::get('password-reset',[AuthenticationController::class,'passwordReset']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('logout', [AuthenticationController::class, 'logout']);
    Route::get('user', [AuthenticationController::class, 'user']);
});