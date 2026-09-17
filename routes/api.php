<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SecuritySyncController;

Route::prefix('security')->middleware('throttle:security-device')->name('api.security.')->group(function () {
    Route::get('/sync', [SecuritySyncController::class, 'sync'])->name('sync');
    Route::get('/master-sync', [SecuritySyncController::class, 'masterSync'])->name('master-sync');
    Route::post('/check-in', [SecuritySyncController::class, 'checkIn'])->name('check-in');
});