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

        // TOTAL PASIEN (Semua Data)
        $totalPasien = Patient::count();

        // ARSIP FISIK (Gudang + Sedang Dipilah)
        $diGudang = Patient::whereIn('manual_status', ['digudang', 'pemilahan'])->count();

        // SUDAH DIMUSNAHKAN
        $sudahMusnah = Patient::where('manual_status', 'dimusnahkan')->count();

        // FILTER: Data yang BELUM masuk Gudang/Musnah/Pemilahan (Murni di Rak Aktif)
        $filterRakAktif = function($q) {
            $q->whereNotIn('manual_status', ['digudang', 'pemilahan', 'dimusnahkan', 'siap_musnah'])
              ->orWhereNull('manual_status');
        };

        $siapMusnah = Patient::where(function($q) use ($now, $filterRakAktif) {
                // A. Yang status manualnya 'siap_musnah'
                $q->where('manual_status', 'siap_musnah')
                  // B. ATAU yang statusnya kosong tapi tanggalnya > 4 tahun
                  ->orWhere(function($sub) use ($now, $filterRakAktif) {
                      // FIX 1: Panggil filter yang sudah dikurung dengan aman di atas
                      $sub->where($filterRakAktif)
                          ->whereHas('lastVisit', function($lv) use ($now) {
                              $lv->where('tgl_kunjungan', '<=', $now->copy()->subYears(4));
                          });
                  });
            })->count();

        // RM AKTIF (< 2 Tahun)
        $aktif = Patient::where($filterRakAktif)
            ->whereHas('lastVisit', function($q) use ($now) {
                $q->where('tgl_kunjungan', '>', $now->copy()->subYears(2));
            })->count();

        // 6. RM INAKTIF (2 - 4 Tahun)
        $inaktif = Patient::where($filterRakAktif)
            ->whereHas('lastVisit', function($q) use ($now) {
                $q->where('tgl_kunjungan', '<=', $now->copy()->subYears(2))
                  ->where('tgl_kunjungan', '>', $now->copy()->subYears(4));
            })->count();

        // DATA LOG
        $recentActivities = RetentionAction::with(['user', 'patient'])->latest()->take(4)->get();

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