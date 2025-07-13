<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ControlsController;
// DashboardController tidak lagi diperlukan di sini
// use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Test endpoint sederhana
Route::get('/test', function () {
    return response()->json(['status' => 'success', 'message' => 'API is working']);
});

// [FIXED] Route untuk menyimpan data dari hardware, memanggil method 'store'
Route::post('/save-data', [ApiController::class, 'store']);

// [FIXED] Route untuk dashboard, sekarang mengambil data dari ApiController yang benar
Route::get('/datastream', [ApiController::class, 'datastream'])->name('api.datastream');

// Route untuk riwayat data grafik (sudah benar)
Route::get('/datastream/history', [ApiController::class, 'history']);

// API endpoint untuk controls yang kompatibel dengan hardware Arduino
Route::get('/controls', [ControlsController::class, 'apiGetControls'])->name('api.controls.get');
Route::post('/controls', [ControlsController::class, 'apiStoreControls'])->name('api.controls.store');

// Endpoint untuk hardware Arduino - GET controls (tanpa authentication)
Route::get('/controls/hardware', [ControlsController::class, 'apiGetControlsHardware'])->name('api.controls.hardware');