<?php

use Illuminate\Support\Facades\Route;

// --- DAFTAR IMPORT CONTROLLER (WAJIB LENGKAP) ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\RetentionController;
use App\Http\Controllers\DestructionController; // <--- INI WAJIB ADA AGAR TIDAK 404
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. RUTE AUTH (LOGIN/LOGOUT)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 2. RUTE PROTEKSI (HARUS LOGIN)
Route::middleware(['auth'])->group(function () {
    
    // Redirect root ke dashboard
    Route::get('/', function() { return redirect('/dashboard'); });

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/activities', [DashboardController::class, 'refreshActivities'])->name('dashboard.activities');
    
    // MASTER DATA
    Route::prefix('master')->group(function () {
        Route::resource('users', UserController::class); 
        
        Route::post('patients/import', [PatientController::class, 'import'])->name('patients.import');
        Route::resource('patients', PatientController::class); 
        
        Route::resource('visits', VisitController::class);
    });
    
    // RETENSI RM
    Route::prefix('retensi')->group(function() {
        Route::get('/', [RetentionController::class, 'index'])->name('retensi.index');
        Route::post('/{id}/verify', [RetentionController::class, 'verifyPhysical'])->name('retensi.verify');
        Route::post('/{id}/move', [RetentionController::class, 'moveToDestruction'])->name('retensi.move');
    });
    
    // PEMUSNAHAN RM (Pastikan controller ini terpanggil)
    Route::prefix('pemusnahan')->group(function() {
        Route::get('/', [DestructionController::class, 'index'])->name('pemusnahan.index');
        Route::post('/{id}/restore', [DestructionController::class, 'restore'])->name('pemusnahan.restore');
        Route::post('/{id}/execute', [DestructionController::class, 'destroyPermanent'])->name('pemusnahan.execute');
    });

    // PELAPORAN
    Route::prefix('laporan')->group(function() {
        // Arahkan ke Controller, BUKAN function() { view(...) }
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('laporan.index');
        
        Route::post('/cetak', [\App\Http\Controllers\ReportController::class, 'printBeritaAcara'])->name('laporan.cetak');
    });
});

