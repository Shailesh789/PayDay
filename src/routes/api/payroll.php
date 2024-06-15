<?php

use App\Http\Controllers\Api\Payroll\PayrunController;
use App\Http\Controllers\Api\Payroll\PayslipController;
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

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'payroll'], function () {
    Route::get('payslip-list', [PayslipController::class, 'index']);
    Route::get('payslip-summary', [PayslipController::class, 'summary']);
    Route::get('payrun-and-badge', [PayrunController::class, 'index']);
    Route::get('payslip/{payslip}', [PayslipController::class,'show']);
    Route::get('payslip/pdf/{payslip}', [\App\Http\Controllers\Tenant\Payroll\PayslipController::class, 'showPdf']);
});