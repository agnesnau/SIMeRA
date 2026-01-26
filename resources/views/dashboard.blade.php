@extends('layouts.app')
@section('title', 'Dashboard Utama')

@section('content')
<style>
    /* MENGUNCI LAYOUT AGAR TIDAK BERUBAH (PERMANEN) */
    .locked-stats-grid {
        display: grid !important;
        gap: 1.5rem !important;
        margin-bottom: 2rem !important;
    }
    
    /* Memaksa 4 kolom di desktop (Layar > 1024px) */
    @media (min-width: 1024px) {
        .locked-stats-grid { 
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important; 
        }
    }
    
    /* Memaksa 2 kolom di tablet (Layar > 640px) */
    @media (min-width: 640px) and (max-width: 1023px) {
        .locked-stats-grid { 
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important; 
        }
    }

    .card-dashboard-final {
        background: white !important;
        border-radius: 0.75rem !important;
        border: 1px solid #f3f4f6 !important;
        padding: 1.5rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
        min-height: 120px !important;
    }

    .card-dashboard-final:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        border-color: #10b981 !important;
    }

    .icon-dashboard-final {
        width: 3.5rem !important;
        height: 3.5rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 0.75rem !important;
        flex-shrink: 0 !important;
    }

    /* Memastikan teks tidak pecah */
    .stat-label-final {
        color: #9ca3af !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        margin-bottom: 0.25rem !important;
    }

    .stat-value-final {
        color: #1f2937 !important;
        font-size: 1.875rem !important;
        font-weight: 900 !important;
        line-height: 1 !important;
    }
</style>

<!-- BAGIAN 1: STATISTIK RINGKAS (LOCKED 4 COLUMNS) -->
<div class="locked-stats-grid">
    
    <!-- Kartu 1: Total Pengguna -->
    <a href="{{ route('users.index') }}" class="card-dashboard-final group">
        <div>
            <p class="stat-label-final">Total Pengguna</p>
            <h3 class="stat-value-final">{{ $totalUser ?? 3 }}</h3>
            <p class="text-[10px] text-emerald-600 mt-2 flex items-center font-bold">
                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span> Terdaftar
            </p>
        </div>
        <div class="icon-dashboard-final bg-blue-50 text-blue-600">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
    </a>

    <!-- Kartu 2: Total Pasien -->
    <a href="{{ route('patients.index') }}" class="card-dashboard-final group">
        <div>
            <p class="stat-label-final">Total Pasien</p>
            <h3 class="stat-value-final">{{ $totalPasien ?? 510 }}</h3>
            <p class="text-[10px] text-gray-400 mt-2 font-medium">Arsip Rekam Medis</p>
        </div>
        <div class="icon-dashboard-final bg-purple-50 text-purple-600">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
    </a>

    <!-- Kartu 3: RM Aktif -->
    <a href="{{ route('retensi.index') }}" class="card-dashboard-final group">
        <div>
            <p class="stat-label-final">RM Aktif</p>
            <h3 class="stat-value-final text-emerald-600">{{ $totalAktif ?? 408 }}</h3>
            <p class="text-[10px] text-emerald-600 mt-2 font-bold uppercase tracking-tighter">Kunjungan < 2 Thn</p>
        </div>
        <div class="icon-dashboard-final bg-emerald-50 text-emerald-600">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </a>

    <!-- Kartu 4: RM Inaktif -->
    <a href="{{ route('retensi.index') }}" class="card-dashboard-final group">
        <div>
            <p class="stat-label-final">RM Inaktif</p>
            <h3 class="stat-value-final text-amber-500">{{ $totalInaktif ?? 102 }}</h3>
            <p class="text-[10px] text-amber-600 mt-2 font-bold uppercase tracking-tighter">Retensi 2-4 Thn</p>
        </div>
        <div class="icon-dashboard-final bg-yellow-50 text-amber-500">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </a>
</div>

<!-- BAGIAN 2: AUDIT TRAIL (STABIL) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-50 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <h3 class="font-extrabold text-gray-800 text-sm">Aktivitas Terkini (Audit Trail)</h3>
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
        </div>
        <span class="text-[10px] text-gray-400 italic">Pembaruan Real-time Aktif</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Waktu Kejadian</th>
                    <th class="px-6 py-4">Pelaksana (User)</th>
                    <th class="px-6 py-4">Tindakan/Aktivitas</th>
                    <th class="px-6 py-4">Detail Informasi</th>
                </tr>
            </thead>
            <tbody id="activity-log-body" class="divide-y divide-gray-50">
                @if(isset($recentActivities))
                    @include('partials.activity_rows', ['recentActivities' => $recentActivities])
                @else
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Sinkronisasi...</span>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('activity-log-body');
        
        function fetchActivities() {
            // Pengamanan protokol agar tidak error di environment pratinjau
            if (!window.location.origin || window.location.protocol.includes('file')) return;
            
            fetch('/dashboard/activities')
                .then(response => response.text())
                .then(html => {
                    // Update hanya jika ada konten dan berbeda untuk mencegah loncatan layout
                    if(html.trim().length > 0 && tableBody.innerHTML !== html) {
                        tableBody.innerHTML = html;
                    }
                })
                .catch(err => console.warn('Penyegaran latar belakang tertunda:', err));
        }

        // Jalankan setiap 3 detik
        setInterval(fetchActivities, 3000);
    });
</script>
@endsection