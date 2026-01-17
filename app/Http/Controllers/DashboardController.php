<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Models\RetentionAction; // Untuk log aktivitas

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Statistik Kartu
        $totalUser = User::count();
        $totalPasien = Patient::count();
        
        // Hitung Aktif vs Inaktif (Menggunakan Collection Filter karena attribute 'current_status' itu virtual)
        // Note: Untuk performa tinggi dengan ribuan data, sebaiknya query database langsung, 
        // tapi untuk skala prototype/kecil, filter collection ini sudah cukup.
        $allPatients = Patient::with('lastVisit')->get();
        $totalInaktif = $allPatients->where('current_status', 'Inaktif')->count();
        $totalAktif = $totalPasien - $totalInaktif;

        // 2. Data Log Aktivitas (Audit Trail)
        // Mengambil 5 aktivitas terakhir dari tabel retention_actions
        $recentActivities = RetentionAction::with(['user', 'patient'])
                            ->latest()
                            ->take(5)
                            ->get();

        return view('dashboard', compact(
            'totalUser', 
            'totalPasien', 
            'totalAktif', 
            'totalInaktif',
            'recentActivities'
        ));
    }
    public function refreshActivities()
    {
        // 1. Ambil 5 data aktivitas terbaru dari database
        $recentActivities = RetentionAction::with(['user', 'patient'])
                            ->latest()
                            ->take(5)
                            ->get();
    
        // 2. Masukkan data itu ke dalam file "partials.activity_rows"
        // 3. render() artinya: ubah kode PHP jadi HTML biasa supaya bisa dibaca browser
        return view('partials.activity_rows', compact('recentActivities'))->render();
    }
}