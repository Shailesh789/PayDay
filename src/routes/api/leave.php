<?php

use App\Http\Controllers\Api\Leave\LeaveAllowanceController;
use App\Http\Controllers\Api\Leave\LeaveController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'leave'], function () {
    Route::get('allowance', [LeaveAllowanceController::class, 'index']);
    Route::get('allowance-details', [LeaveAllowanceController::class, 'allowanceDetails']);
    Route::get('availability', [LeaveAllowanceController::class, 'availability']);
    Route::get('type', [LeaveAllowanceController::class, 'type']);
    Route::get('record', [LeaveController::class, 'record']);
    Route::get('list/view', [LeaveController::class, 'listView']);
    Route::get('summary', [LeaveController::class, 'summary']);
    Route::get('log/{leave_id}', [LeaveController::class, 'log']);
    Route::post('store', [LeaveController::class, 'store']);
    Route::post('cancel', [LeaveController::class, 'cancel']);
});