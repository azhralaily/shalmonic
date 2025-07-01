<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ControlsController;
use App\Http\Controllers\DataOverviewController;
use App\Http\Controllers\UserManagementController;


Route::get('/', function () {
    return redirect()->route('login');
});

// Hak akses sesuai role
Route::middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/controls', [ControlsController::class, 'index'])->name('controls');
    Route::post('/controls', [ControlsController::class, 'store'])->name('controls.store');
    Route::get('/dataoverview', [DataOverviewController::class, 'index'])->name('dataoverview');
    Route::get('/dataoverview/fetch', [DataOverviewController::class, 'fetchData'])->name('dataoverview.fetch');
    Route::get('/dataoverview/export', [DataOverviewController::class, 'exportCsv'])->name('dataoverview.export');
    Route::post('/update-manual-values', [DashboardController::class, 'updateManualValues'])->name('update.manual.values');
});

Route::middleware(['auth', 'role:guest'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Profile routes tetap bisa diakses semua user yang login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/manage-accounts', [UserManagementController::class, 'index'])->name('manage.accounts');
    Route::post('/manage-accounts/update', [UserManagementController::class, 'update'])->name('manage.accounts.update');
    Route::post('/manage-accounts/delete', [UserManagementController::class, 'delete'])->name('manage.accounts.delete');
    Route::post('/manage-accounts/create', [UserManagementController::class, 'create'])->name('manage.accounts.create');
});

require __DIR__.'/auth.php';
