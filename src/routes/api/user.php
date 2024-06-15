<?php

use App\Http\Controllers\Api\User\ProfileController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:sanctum', 'prefix'=>'user'], function () {
    Route::get('profile', [ProfileController::class, 'index']);
    Route::post('profile/update', [ProfileController::class, 'update']);
    Route::post('change-picture', [ProfileController::class, 'changePicture']);
    Route::post('change-password', [ProfileController::class, 'changePassword']);
});