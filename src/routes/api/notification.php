<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Notification\NotificationController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('notifications', [NotificationController::class,'index']);
    Route::get('notification/read/{id}', [NotificationController::class,'markAsRead']);
    Route::get('notification/all/read', [NotificationController::class,'markAsAllRead']);
});