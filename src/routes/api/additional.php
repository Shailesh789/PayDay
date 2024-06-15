<?php

use App\Http\Controllers\Api\Calendar\CalendarController;
use App\Http\Controllers\Core\Setting\StatusController;
use App\Http\Controllers\Tenant\Settings\GeneralSettingController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('calendar/holidays', [CalendarController::class, 'getHoliDays']);
    Route::get('status', [StatusController::class, 'getStatusTypeWise']);
    Route::get('basic-information', [GeneralSettingController::class, 'formattedSettings']);
});