<?php

use App\Http\Controllers\Api\Emergency\EmergencyContactController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'emergency'], function () {
    Route::get('index', [EmergencyContactController::class, 'index']);
    Route::post('store', [EmergencyContactController::class, 'store']);
    Route::post('update', [EmergencyContactController::class, 'update']);
    Route::post('delete', [EmergencyContactController::class, 'destroy']);
});
