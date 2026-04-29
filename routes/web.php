<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\RetentionController;
use App\Http\Controllers\DestructionController; 
use App\Http\Controllers\EksekusiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PemilahanController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem SIMeRA (Fixed Priority)
|--------------------------------------------------------------------------
*/


// --- AUTHENTICATION ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// ==============================================================================
// PRIORITAS 1: GRUP EKSEKUTOR (ADMIN & PETUGAS)
// ==============================================================================
Route::middleware(['auth', 'level:admin,petugas'])->group(function () {
    
    // --- PASIEN & KUNJUNGAN (CRUD) ---
    Route::resource('master/patients', PatientController::class)->except(['index', 'show']);
    Route::post('master/patients/import', [PatientController::class, 'import'])->name('patients.import');
    Route::post('master/patients/bulk-action', [PatientController::class, 'bulkAction'])->name('patients.bulkAction');
    
    Route::resource('master/visits', VisitController::class)->except(['index', 'show']);
    Route::post('master/visits/bulk-action', [VisitController::class, 'bulkAction'])->name('visits.bulkAction');

    // --- AKSI RETENSI ---
    Route::prefix('retensi')->group(function() {
        Route::post('/{id}/ke-pemilahan', [RetentionController::class, 'sendToSorting'])->name('retensi.sendToSorting');
        Route::post('/{id}/move-destruction', [RetentionController::class, 'moveToDestruction'])->name('retensi.moveToDestruction');
        Route::post('/bulk', [RetentionController::class, 'bulkAction'])->name('retensi.bulkAction');
        
        // HANYA PETUGAS YANG BISA MENYELESAIKAN (PINDAH KE GUDANG)
        Route::post('/pemilahan/{id}/selesai', [PemilahanController::class, 'finishSorting'])->name('retensi.pemilahan.selesai');
        Route::post('/sorting/finish-all', [PemilahanController::class, 'finishAllSorting'])->name('sorting.finishAll'); 
        
        // --- ROUTE BATAL & KOREKSI (PEMILAHAN) ---
        Route::post('/pemilahan/batal/{id}', [PemilahanController::class, 'batalUsulPemilahan'])->name('pemilahan.batal');
        Route::post('/pemilahan/koreksi/{id}', [PemilahanController::class, 'koreksiEksekusi'])->name('pemilahan.koreksi');
    });

    // --- AKSI PEMUSNAHAN ---
    Route::prefix('pemusnahan')->group(function() {
        Route::post('/{id}/assess', [DestructionController::class, 'storeAssessment'])->name('pemusnahan.assess');
        Route::post('/bulk', [DestructionController::class, 'bulkAction'])->name('pemusnahan.bulkAction');
        Route::post('/{id}/restore', [DestructionController::class, 'restore'])->name('pemusnahan.restore');
        
        Route::post('/eksekusi/usul', [EksekusiController::class, 'usulEksekusi'])->name('eksekusi.usul');
        
        // HANYA PETUGAS YANG BISA EKSEKUSI PEMUSNAHAN FISIK
        Route::post('/eksekusi/{id}/selesai', [EksekusiController::class, 'selesai'])->name('eksekusi.selesai');
        Route::post('/eksekusi/finish-all', [EksekusiController::class, 'destroyAll'])->name('eksekusi.destroyAll'); 
        
        // --- ROUTE BATAL & KOREKSI (EKSEKUSI) ---
        Route::post('/eksekusi/batal/{id}', [EksekusiController::class, 'batalUsul'])->name('eksekusi.batal');
        Route::post('/eksekusi/koreksi/{id}', [EksekusiController::class, 'koreksiEksekusi'])->name('eksekusi.koreksi');    
    });

});


// ==============================================================================
// PRIORITAS 2: GRUP VIEW ONLY & APPROVAL (ADMIN, PETUGAS, SUPERVISOR/KAPUS)
// ==============================================================================
Route::middleware(['auth', 'level:admin,petugas,supervisor,kapuskesmas,kepala'])->group(function () {
    
    Route::get('/', function() { return redirect()->route('dashboard'); });

    // --- DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/activities', [DashboardController::class, 'refreshActivities'])->name('dashboard.activities');
    
    // --- MASTER DATA (READ ONLY) ---
    Route::resource('master/patients', PatientController::class)->only(['index', 'show']);
    Route::resource('master/visits', VisitController::class)->only(['index', 'show']);

    // --- RETENSI (READ ONLY & APPROVAL) ---
    Route::prefix('retensi')->group(function() {
        Route::get('/', [RetentionController::class, 'index'])->name('retensi.index');
        Route::get('/pemilahan', [PemilahanController::class, 'index'])->name('retensi.pemilahan'); 
        Route::get('/proposed', [RetentionController::class, 'proposed'])->name('retensi.proposed');
        Route::get('/history', [RetentionController::class, 'history'])->name('retensi.history');
        
        // SEMUA BISA AKSES ROUTE INI, TAPI CONTROLLER YANG MENYELEKSI HAK KAPUS
        Route::post('/pemilahan/approve', [PemilahanController::class, 'approve'])->name('retensi.pemilahan.approve');
    });
    
    // --- PEMUSNAHAN (READ ONLY & APPROVAL) ---
    Route::prefix('pemusnahan')->group(function() {
        Route::get('/', [DestructionController::class, 'index'])->name('pemusnahan.index');
        Route::get('/history', [DestructionController::class, 'history'])->name('pemusnahan.history');
        Route::get('/eksekusi', [EksekusiController::class, 'index'])->name('pemusnahan.eksekusi');
        
        // SEMUA BISA AKSES ROUTE INI, TAPI CONTROLLER YANG MENYELEKSI HAK KAPUS
        Route::post('/eksekusi/approve', [EksekusiController::class, 'approve'])->name('eksekusi.approve');
    });
});


// ==============================================================================
// GRUP 3: PELAPORAN (ADMIN & SUPERVISOR/KAPUS)
// ==============================================================================
Route::middleware(['auth', 'level:admin,supervisor,kapuskesmas,kepala'])->group(function () {
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/cetak', [ReportController::class, 'printBeritaAcara'])->name('cetak');
        Route::get('/reprint/{id}', [ReportController::class, 'reprint'])->name('reprint');
    });
});


// ==============================================================================
// GRUP 4: SUPER ADMIN (HANYA ADMIN)
// ==============================================================================
Route::middleware(['auth', 'level:admin'])->group(function () {
    Route::resource('master/users', UserController::class); 
});