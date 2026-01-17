@extends('layouts.app')
@section('title', 'Dashboard Utama')

@section('content')

<!-- BAGIAN 1: STATISTIK RINGKAS -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Kartu 1: Total Pengguna -->
    <a href="{{ route('users.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition-all hover:shadow-md hover:border-emerald-200 cursor-pointer">
        <div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider group-hover:text-emerald-600 transition-colors">Total Pengguna</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalUser ?? 0 }}</h3>
            <p class="text-xs text-emerald-600 mt-1 flex items-center">
                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-1 animate-pulse"></span> Terdaftar dalam Sistem
            </p>
        </div>
        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-100 transition-colors">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
    </a>

    <!-- Kartu 2: Total Pasien -->
    <a href="{{ route('patients.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition-all hover:shadow-md hover:border-emerald-200 cursor-pointer">
        <div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider group-hover:text-purple-600 transition-colors">Total Pasien</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalPasien ?? 0 }}</h3>
            <p class="text-xs text-gray-400 mt-1">Total Arsip Rekam Medis</p>
        </div>
        <div class="p-3 bg-purple-50 text-purple-600 rounded-lg group-hover:bg-purple-100 transition-colors">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
    </a>

    <!-- Kartu 3: RM Aktif -->
    <a href="{{ route('retensi.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition-all hover:shadow-md hover:border-emerald-200 cursor-pointer">
        <div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider group-hover:text-emerald-600 transition-colors">RM Aktif</p>
            <h3 class="text-3xl font-bold text-emerald-600 mt-1">{{ $totalAktif ?? 0 }}</h3>
            <p class="text-xs text-emerald-600 mt-1">Kunjungan < 2 Tahun</p>
        </div>
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-100 transition-colors">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </a>

    <!-- Kartu 4: RM Inaktif -->
    <a href="{{ route('retensi.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition-all hover:shadow-md hover:border-yellow-200 cursor-pointer">
        <div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider group-hover:text-yellow-600 transition-colors">RM Inaktif</p>
            <h3 class="text-3xl font-bold text-yellow-600 mt-1">{{ $totalInaktif ?? 0 }}</h3>
            <p class="text-xs text-yellow-600 mt-1">Masa Retensi 2-4 Tahun</p>
        </div>
        <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg group-hover:bg-yellow-100 transition-colors">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </a>
</div>

<!-- BAGIAN 2: AUDIT TRAIL (RIWAYAT AKTIVITAS) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <div class="flex items-center gap-2">
            <h3 class="font-bold text-gray-700">Aktivitas Terkini (Audit Trail)</h3>
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
        </div>
        <span class="text-xs text-gray-500 italic">Pembaruan Waktu Nyata (Real-time)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                <tr>
                    <th class="px-6 py-3">Waktu Kejadian</th>
                    <th class="px-6 py-3">Pelaksana (User)</th>
                    <th class="px-6 py-3">Tindakan/Aktivitas</th>
                    <th class="px-6 py-3">Detail Informasi</th>
                </tr>
            </thead>
            <tbody id="activity-log-body" class="divide-y divide-gray-100">
                @if(isset($recentActivities))
                    @include('partials.activity_rows', ['recentActivities' => $recentActivities])
                @else
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">Menghubungkan ke pangkalan data...</td>
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
            // Pengamanan agar fetch tidak berjalan jika protokol tidak valid (blob/file)
            if (!window.location.origin || window.location.protocol === 'blob:' || window.location.protocol === 'file:') return;
            try {
                fetch('/dashboard/activities')
                    .then(response => response.text())
                    .then(html => {
                        if(html.trim().length > 0) tableBody.innerHTML = html;
                    })
                    .catch(err => console.warn('Gagal sinkronisasi aktivitas:', err));
            } catch (e) {}
        }
        // Jalankan setiap 3 detik
        setInterval(fetchActivities, 3000);
    });
</script>
@endsection