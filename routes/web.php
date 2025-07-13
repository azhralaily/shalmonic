<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ControlsController;
use App\Http\Controllers\DataOverviewController;
use App\Http\Controllers\UserManagementController;

// Rute utama dialihkan ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Grup untuk semua pengguna yang sudah login
Route::middleware(['auth'])->group(function () {
    
    // Dashboard bisa diakses oleh semua role yang login
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rute khusus untuk admin dan operator
    Route::get('/controls', [ControlsController::class, 'index'])->name('controls');
    Route::post('/controls', [ControlsController::class, 'store'])->name('controls.store');
    Route::post('/controls/manual-update', [ControlsController::class, 'updateManualValues'])->name('update.manual.values');
    
    // [FIXED] Perbaikan pada route DataOverview
    Route::get('/dataoverview', [DataOverviewController::class, 'index'])->name('dataoverview');
    Route::get('/dataoverview/fetch', [DataOverviewController::class, 'fetch'])->name('dataoverview.fetch'); // Method diubah dari fetchData ke fetch
    Route::get('/dataoverview/export', [DataOverviewController::class, 'export'])->name('dataoverview.export'); // Method diubah dari exportCsv ke export

    // [REMOVED] Route duplikat ini dihapus untuk menghindari konflik
    // Route::post('/update-manual-values', [DashboardController::class, 'updateManualValues'])->name('update.manual.values');

    // Rute khusus untuk admin
    Route::get('/manage-accounts', [UserManagementController::class, 'index'])->name('manage.accounts');
    Route::post('/manage-accounts/update', [UserManagementController::class, 'update'])->name('manage.accounts.update');
    Route::post('/manage-accounts/delete', [UserManagementController::class, 'delete'])->name('manage.accounts.delete');
    Route::post('/manage-accounts/create', [UserManagementController::class, 'create'])->name('manage.accounts.create');

    // Rute profil bisa diakses semua role yang login
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';