<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ControlsController;

// Test endpoint sederhana
Route::get('/test', function () {
    return response()->json(['status' => 'success', 'message' => 'API is working']);
});

// Rute untuk menerima data POST dari IoT device (tanpa authentication)
Route::post('/save-data', [ApiController::class, 'storeDatastream']);
// Rute untuk mengambil datastream (jika digunakan oleh dashboard atau API lain)
Route::get ('/datastream', [DashboardController::class, 'apiDatastream'])->name('api.datastream');
Route::get('/datastream/history', [ApiController::class, 'history']);

// API endpoint untuk controls yang kompatibel dengan hardware Arduino
Route::get('/controls', [ControlsController::class, 'apiGetControls'])->name('api.controls.get');
Route::post('/controls', [ControlsController::class, 'apiStoreControls'])->name('api.controls.store');

// Endpoint untuk hardware Arduino - GET controls (tanpa authentication)
Route::get('/controls/hardware', [ControlsController::class, 'apiGetControlsHardware'])->name('api.controls.hardware');