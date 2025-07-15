<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ControlsController;
use App\Http\Controllers\DataOverviewController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\UserManagementController;

// Rute utama dialihkan ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// ===================================================================
// GRUP UNTUK SEMUA PENGGUNA YANG SUDAH LOGIN (ADMIN, OPERATOR, GUEST)
// ===================================================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard bisa diakses oleh semua
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
        
    // Halaman Profil bisa diakses oleh semua
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// =====================================================
// GRUP HANYA UNTUK ADMIN DAN OPERATOR
// =====================================================
Route::middleware(['auth', RoleMiddleware::class . ':admin,operator'])->group(function () {
    Route::get('/controls', [ControlsController::class, 'index'])->name('controls');
    Route::post('/controls', [ControlsController::class, 'store'])->name('controls.store');
    Route::post('/controls/manual-update', [ControlsController::class, 'updateManualValues'])->name('update.manual.values');

    // [DIPINDAHKAN] Data Overview bisa diakses oleh semua
    Route::get('/dataoverview', [DataOverviewController::class, 'index'])->name('dataoverview');
    Route::get('api/dataoverview/fetch', [DataOverviewController::class, 'fetch'])->name('dataoverview.fetch');
    Route::get('/dataoverview/export', [DataOverviewController::class, 'export'])->name('dataoverview.export');
});

// ==========================================
// GRUP HANYA UNTUK ADMIN
// ==========================================
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('index');
    Route::post('/', [UserManagementController::class, 'store'])->name('store');
    
    // Route untuk menampilkan halaman edit
    Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
    // Route untuk memproses update
    Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
    // Route untuk menghapus
    Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';