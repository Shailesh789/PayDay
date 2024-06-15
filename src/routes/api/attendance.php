<?php

use App\Http\Controllers\Api\Attendance\AttendanceController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'attendance'], function () {
    Route::get('check-is-punch-in', [AttendanceController::class, 'checkIsPunchIn']);
    Route::post('punch-in', [AttendanceController::class, 'punchIn']);
    Route::post('punch-out', [AttendanceController::class, 'punchOut']);
    Route::get('daily-log', [AttendanceController::class, 'dailyLog']);
    Route::get('details/{attendance_details}', [AttendanceController::class, 'show']);
    Route::post('request/{attendance_details}', [AttendanceController::class, 'attendanceRequest']);
    Route::get('log/{attendance_details}', [AttendanceController::class, 'attendanceLog']);
    Route::post('add-request', [AttendanceController::class, 'addRequest']);
    Route::get('summary', [AttendanceController::class, 'summary']);
    Route::get('summaries-data-logs', [AttendanceController::class, 'summaryDatatable']);
    Route::get('details-summary', [AttendanceController::class, 'attendanceDetails']);
    Route::get('status/cancel/{details}', [AttendanceController::class, 'cancelAttendance'])
        ->middleware('check_behavior:access_all_departments');
    Route::patch('break-start/{attendance_details}', [AttendanceController::class, 'startBreak'])
        ->name('start.break');
    Route::patch('break-end/{attendance_details}', [AttendanceController::class, 'endBreak'])
        ->name('end.break');
});