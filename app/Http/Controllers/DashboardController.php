<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\RetentionAction; 
use Carbon\Carbon; 

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();

        // 1. TOTAL PASIEN (Semua Data)
        // Contoh: 31
        $totalPasien = Patient::count();


        // 2. ARSIP FISIK (Gudang + Sedang Dipilah)
        // PERBAIKAN: Masukkan status 'pemilahan' ke sini agar data tidak hilang!
        // Logika: Pasien yang di gudang ATAU sedang dipilah fisik, dianggap arsip fisik.
        $diGudang = Patient::whereIn('manual_status', ['digudang', 'pemilahan'])->count();


        // 3. SUDAH DIMUSNAHKAN
        $sudahMusnah = Patient::where('manual_status', 'dimusnahkan')->count();


        // FILTER: Data yang BELUM masuk Gudang/Musnah/Pemilahan (Murni di Rak Aktif)
        $filterRakAktif = function($q) {
            $q->whereNotIn('manual_status', ['digudang', 'pemilahan', 'dimusnahkan', 'siap_musnah'])
              ->orWhereNull('manual_status');
        };

        // 4. SIAP MUSNAH / KANDIDAT RETENSI (> 5 Tahun)
        // Syarat: Ada di rak aktif TAPI sudah tua, ATAU status manualnya 'siap_musnah'
        $siapMusnah = Patient::where(function($q) use ($now) {
                // A. Yang status manualnya 'siap_musnah'
                $q->where('manual_status', 'siap_musnah')
                  // B. ATAU yang statusnya kosong tapi tanggalnya > 5 tahun
                  ->orWhere(function($sub) use ($now) {
                      $sub->whereNotIn('manual_status', ['digudang', 'pemilahan', 'dimusnahkan', 'siap_musnah'])
                          ->orWhereNull('manual_status')
                          ->whereHas('lastVisit', function($lv) use ($now) {
                              $lv->where('tgl_kunjungan', '<=', $now->copy()->subYears(5));
                          });
                  });
            })->count();


        // 5. RM AKTIF (< 2 Tahun)
        $aktif = Patient::where($filterRakAktif)
            ->whereHas('lastVisit', function($q) use ($now) {
                $q->where('tgl_kunjungan', '>', $now->copy()->subYears(2));
            })->count();


        // 6. RM INAKTIF (2 - 5 Tahun)
        $inaktif = Patient::where($filterRakAktif)
            ->whereHas('lastVisit', function($q) use ($now) {
                $q->where('tgl_kunjungan', '<=', $now->copy()->subYears(2))
                  ->where('tgl_kunjungan', '>', $now->copy()->subYears(5));
            })->count();


        // DATA LOG
        $recentActivities = RetentionAction::with(['user', 'patient'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalPasien', 'aktif', 'inaktif', 'siapMusnah', 
            'diGudang', 'sudahMusnah', 'recentActivities'
        ));
    }

    public function refreshActivities()
    {
        $recentActivities = RetentionAction::with(['user', 'patient'])->latest()->take(5)->get();
        return view('partials.activity_rows', compact('recentActivities'))->render();
    }
}