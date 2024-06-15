<?php

use App\Http\Controllers\Api\Employee\EmployeeBankDetailController;
use App\Http\Controllers\Api\Employee\EmployeeController;
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

Route::group(['middleware' => 'auth:sanctum', 'prefix'=> 'employee'], function () {
    Route::get('job-history', [EmployeeController::class, 'jobHistory']);
    Route::get('salary-overview', [EmployeeController::class, 'salaryOverview']);
    Route::get('addresses', [EmployeeController::class, 'addresses']);
    Route::post('address-update', [EmployeeController::class, 'addressUpdate']);
    Route::post('address-delete', [EmployeeController::class, 'addressDelete']);
    Route::get('asset', [EmployeeController::class, 'asset']);
    Route::get('asset/{asset}', [EmployeeController::class, 'assetDetails']);
    Route::apiResource('bank-information',EmployeeBankDetailController::class);
    Route::get('announcement', [EmployeeController::class, 'announcement']);
});