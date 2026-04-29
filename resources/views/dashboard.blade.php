@extends('layouts.app')
@section('title', 'Dashboard Utama')

@section('content')
<style>
    /* LAYOUT 6 KARTU (3 Kolom x 2 Baris) */
    .locked-stats-grid {
        display: grid !important;
        gap: 1rem !important;
        margin-bottom: 2rem !important;
    }
    
    /* Desktop: 3 Kolom */
    @media (min-width: 1024px) {
        .locked-stats-grid { 
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important; 
        }
    }
    
    /* Tablet: 2 Kolom */
    @media (min-width: 640px) and (max-width: 1023px) {
        .locked-stats-grid { 
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important; 
        }
    }

    .card-dashboard-final {
        background: white !important;
        border-radius: 0.75rem !important;
        border: 1px solid #f3f4f6 !important;
        padding: 1.25rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
        min-height: 100px !important;
    }

    .card-dashboard-final:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }

    /* Warna Border Hover */
    .hover-purple:hover { border-color: #9333ea !important; }
    .hover-emerald:hover { border-color: #10b981 !important; }
    .hover-amber:hover { border-color: #f59e0b !important; }
    .hover-rose:hover { border-color: #e11d48 !important; } /* Siap Musnah */
    .hover-blue:hover { border-color: #3b82f6 !important; }
    .hover-gray:hover { border-color: #4b5563 !important; } /* Sudah Musnah */

    .icon-dashboard-final {
        width: 3rem !important;
        height: 3rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 0.75rem !important;
        flex-shrink: 0 !important;
    }

    .stat-label-final {
        color: #9ca3af !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        margin-bottom: 0.25rem !important;
    }

    .stat-value-final {
        color: #1f2937 !important;
        font-size: 1.5rem !important;
        font-weight: 900 !important;
        line-height: 1 !important;
    }
</style>

<div class="locked-stats-grid">
    
    <a href="{{ route('patients.index') }}" class="card-dashboard-final hover-purple group">
        <div>
            <p class="stat-label-final">Total Pasien</p>
            <h3 class="stat-value-final text-purple-600">{{ $totalPasien ?? 0 }}</h3>
            <p class="text-[10px] text-gray-400 mt-1 font-medium">Semua Data</p>
        </div>
        <div class="icon-dashboard-final bg-purple-50 text-purple-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
    </a>

    <a href="{{ route('patients.index', ['status' => 'Aktif']) }}" class="card-dashboard-final hover-emerald group">
        <div>
            <p class="stat-label-final">RM Aktif</p>
            <h3 class="stat-value-final text-emerald-600">{{ $aktif ?? 0 }}</h3>
            <p class="text-[10px] text-emerald-600 mt-1 font-bold">< 2 Tahun</p>
        </div>
        <div class="icon-dashboard-final bg-emerald-50 text-emerald-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </a>

    <a href="{{ route('patients.index', ['status' => 'Inaktif']) }}" class="card-dashboard-final hover-amber group">
        <div>
            <p class="stat-label-final">RM Inaktif</p>
            <h3 class="stat-value-final text-amber-500">{{ $inaktif ?? 0 }}</h3>
            <p class="text-[10px] text-amber-600 mt-1 font-bold">Retensi 2-5 Thn</p>
        </div>
        <div class="icon-dashboard-final bg-yellow-50 text-amber-500">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </a>

    <a href="{{ route('patients.index', ['status' => 'Siap Musnah']) }}" class="card-dashboard-final hover-rose group">
        <div>
            <p class="stat-label-final">Siap Musnah</p>
            <h3 class="stat-value-final text-rose-600">{{ $siapMusnah ?? 0 }}</h3>
            <p class="text-[10px] text-rose-600 mt-1 font-bold">> 5 Tahun (Kandidat)</p>
        </div>
        <div class="icon-dashboard-final bg-rose-50 text-rose-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
    </a>

    <a href="{{ route('patients.index', ['status' => 'digudang']) }}" class="card-dashboard-final hover-blue group">
        <div>
            <p class="stat-label-final">Arsip Fisik</p>
            <h3 class="stat-value-final text-blue-600">{{ $diGudang ?? 0 }}</h3>
            <p class="text-[10px] text-blue-600 mt-1 font-bold">Di Gudang</p>
        </div>
        <div class="icon-dashboard-final bg-blue-50 text-blue-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
        </div>
    </a>

    <a href="{{ route('patients.index', ['status' => 'dimusnahkan']) }}" class="card-dashboard-final hover-gray group">
        <div>
            <p class="stat-label-final">Sudah Musnah</p>
            <h3 class="stat-value-final text-gray-600">{{ $sudahMusnah ?? 0 }}</h3>
            <p class="text-[10px] text-gray-500 mt-1 font-bold">Permanen</p>
        </div>
        <div class="icon-dashboard-final bg-gray-100 text-gray-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </div>
    </a>

</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-50 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <h3 class="font-extrabold text-gray-800 text-sm">Aktivitas Terkini (Audit Trail)</h3>
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
        </div>
        <span class="text-[10px] text-gray-400 italic">Real-time</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Aksi</th>
                    <th class="px-6 py-4">Detail</th>
                </tr>
            </thead>
            <tbody id="activity-log-body" class="divide-y divide-gray-50">
                @if(isset($recentActivities))
                    @include('partials.activity_rows', ['recentActivities' => $recentActivities])
                @else
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">Loading...</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('activity-log-body');
        function fetchActivities() {
            if (!window.location.origin || window.location.protocol.includes('file')) return;
            fetch('/dashboard/activities')
                .then(r => r.text())
                .then(html => { if(html.trim().length > 0 && tableBody.innerHTML !== html) tableBody.innerHTML = html; })
                .catch(e => console.warn(e));
        }
        setInterval(fetchActivities, 3000);
    });
</script>
@endsection