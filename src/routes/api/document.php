<?php

use App\Http\Controllers\Api\Document\DocumentController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'document'], function () {
    Route::get('index', [DocumentController::class, 'index']);
    Route::post('store', [DocumentController::class, 'store']);
    Route::post('update', [DocumentController::class, 'update']);
    Route::post('delete', [DocumentController::class, 'destroy']);
});