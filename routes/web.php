<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\RetentionController;
use App\Http\Controllers\DestructionController; 
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PemilahanController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem SIMeRA (Puskesmas Silo 1)
|--------------------------------------------------------------------------
*/

// --- AUTHENTICATION ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    
    Route::get('/', function() { return redirect('/dashboard'); });

    // --- DASHBOARD & MONITORING ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/activities', [DashboardController::class, 'refreshActivities'])->name('dashboard.activities');
    
    // --- MASTER DATA (STRUKTUR LAMA) ---
    Route::prefix('master')->group(function () {
        // Manajemen Pengguna
        Route::resource('users', UserController::class); 
        
        // Manajemen Pasien (Lengkap dengan Import & Bulk Action)
        Route::post('patients/import', [PatientController::class, 'import'])->name('patients.import');
        Route::post('patients/bulk-action', [PatientController::class, 'bulkAction'])->name('patients.bulkAction');
        Route::resource('patients', PatientController::class); // Support Full-Page Show (Resume Medis)
        
        // Manajemen Kunjungan (Lengkap dengan Bulk Action)
        Route::post('visits/bulk-action', [VisitController::class, 'bulkAction'])->name('visits.bulkAction');
        Route::resource('visits', VisitController::class); // Support Full-Page Show (Lembar Medis)
    });
    
    // --- MODUL RETENSI (STRUKTUR LAMA + PROPOSED/HISTORY) ---
    Route::prefix('retensi')->group(function() {
        // Daftar Utama
        Route::get('/', [RetentionController::class, 'index'])->name('retensi.index');
        
        // FITUR BARU: Pemilahan RM (Wajib ada agar rute di Canvas tidak error)
        Route::get('/pemilahan', [PemilahanController::class, 'index'])->name('retensi.pemilahan');
        Route::post('/pemilahan/{id}/selesai', [PemilahanController::class, 'finishSorting'])->name('retensi.pemilahan.selesai');
        
        // Aksi Transisi
        Route::post('/{id}/ke-pemilahan', [RetentionController::class, 'sendToSorting'])->name('retensi.sendToSorting');
        Route::post('/{id}/verify', [RetentionController::class, 'verifyPhysical'])->name('retensi.verify');
        Route::post('/{id}/move', [RetentionController::class, 'moveToDestruction'])->name('retensi.move');
        
        Route::get('/proposed', [RetentionController::class, 'proposed'])->name('retensi.proposed');
        Route::get('/history', [RetentionController::class, 'history'])->name('retensi.history');
        Route::post('/bulk', [RetentionController::class, 'bulkAction'])->name('retensi.bulkAction');
    });
    
    // --- MODUL PEMUSNAHAN (STRUKTUR LAMA + PROPOSED/HISTORY) ---
    Route::prefix('pemusnahan')->group(function() {
        Route::get('/', [DestructionController::class, 'index'])->name('pemusnahan.index');
        Route::get('/proposed', [DestructionController::class, 'proposed'])->name('pemusnahan.proposed');
        Route::get('/history', [DestructionController::class, 'history'])->name('pemusnahan.history');
        Route::post('/bulk', [DestructionController::class, 'bulkAction'])->name('pemusnahan.bulkAction');

        // ALIAS UNTUK MENCEGAH ERROR (SINKRON DENGAN VIEW BAHASA INGGRIS)
        Route::get('/index-alias', [DestructionController::class, 'index'])->name('destruction.index');
        Route::post('/bulk-alias-pm', [DestructionController::class, 'bulkAction'])->name('destruction.bulkAction');
        
        // Aksi Individu
        Route::post('/{id}/restore', [DestructionController::class, 'restore'])->name('pemusnahan.restore');
        Route::post('/{id}/execute', [DestructionController::class, 'destroyPermanent'])->name('pemusnahan.execute');
    });

    // --- MODUL PELAPORAN ---
Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::post('/cetak', [ReportController::class, 'printBeritaAcara'])->name('cetak');

    Route::get('/reprint/{id}', [ReportController::class, 'reprint'])->name('reprint');

});
    });