@extends('layouts.app')
@section('title', 'Lembar Kunjungan Medis')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    
    <!-- HEADER: GAYA NOTA/KUITANSI MEDIS -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-800 px-8 py-8 text-white flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
            <!-- Dekorasi Background -->
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
                <svg width="200" height="200" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14h-2V9h-2V7h4v10z"/></svg>
            </div>

            <div class="relative z-10 flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] mb-1">Nomor Registrasi Kunjungan</p>
                    <h1 class="text-3xl font-mono font-black tracking-tighter">{{ $visit->no_registrasi }}</h1>
                </div>
            </div>
            
            <div class="relative z-10 text-right">
                <p class="text-sm font-bold opacity-80">{{ $visit->tgl_kunjungan->translatedFormat('l, d F Y') }}</p>
                <span class="inline-block mt-2 px-3 py-1 bg-emerald-500 rounded-lg text-[10px] font-black uppercase tracking-widest">Selesai Dilayani</span>
            </div>
        </div>

        <!-- IDENTITAS PASIEN (DATA SINGKAT) -->
        <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8 items-center bg-slate-50/50">
            <div class="md:col-span-2">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pasien Terdaftar</p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-lg border-2 border-white shadow-sm">
                        {{ substr($visit->patient->nama_pasien, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 uppercase">{{ $visit->patient->nama_pasien }}</h3>
                        <p class="text-sm font-mono text-slate-500 font-bold">INDEX: {{ $visit->patient->no_rm }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <a href="{{ route('patients.show', $visit->patient->id) }}" class="flex items-center justify-center gap-2 py-3 bg-white text-emerald-700 border border-emerald-200 rounded-xl text-xs font-black hover:bg-emerald-50 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    LIHAT RESUME LENGKAP
                </a>
            </div>
        </div>
    </div>

    <!-- DETAIL PEMERIKSAAN -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center gap-2">
            <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
            <h3 class="font-black text-slate-700 text-xs uppercase tracking-widest">Detail Pelayanan & Diagnosa</h3>
        </div>
        
        <div class="p-10 space-y-10">
            <!-- Baris 1: Poli & Dokter -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Poli / Unit Tujuan</label>
                    <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 flex items-center gap-3">
                        <div class="p-2 bg-blue-600 text-white rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span class="text-lg font-black text-blue-900 uppercase">{{ $visit->poli_tujuan }}</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dokter Penanggung Jawab</label>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center gap-3">
                        <div class="p-2 bg-slate-700 text-white rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-lg font-bold text-slate-800">{{ $visit->dokter }}</span>
                    </div>
                </div>
            </div>

            <!-- Baris 2: Diagnosa Akhir -->
            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Diagnosa Akhir / Keterangan Medis</label>
                <div class="relative p-8 bg-amber-50 rounded-3xl border border-amber-100 italic text-amber-900 text-lg leading-relaxed shadow-inner">
                    <svg class="absolute top-4 left-4 w-8 h-8 text-amber-200" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H16.017C14.9124 8 14.017 7.10457 14.017 6V5C14.017 3.89543 14.9124 3 16.017 3H19.017C21.2261 3 23.017 4.79086 23.017 7V15C23.017 18.3137 20.3307 21 17.017 21H14.017ZM1 15V9C1 6.79086 2.79086 5 5 5H8C9.10457 5 10 5.89543 10 7V8C10 9.10457 9.10457 10 8 10H5C4.44772 10 4 10.4477 4 11V15C4 15.5523 4.44772 16 5 16H8C9.10457 16 10 16.8954 10 18V21H7C3.68629 21 1 18.3137 1 15Z"/></svg>
                    <p class="relative z-10 pl-4">"{{ $visit->diagnosa }}"</p>
                </div>
            </div>
        </div>

        <!-- FOOTER: INFO SISTEM -->
        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3 text-xs text-slate-400 font-bold uppercase tracking-wider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Dicatat oleh: <span class="text-slate-600">{{ $visit->user->nama_lengkap ?? 'Sistem' }}</span>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-5 py-2 bg-white text-slate-600 border border-slate-200 rounded-xl text-xs font-black hover:bg-slate-50 transition shadow-sm">
                    CETAK LEMBAR
                </button>
                <a href="{{ route('visits.index') }}" class="px-5 py-2 bg-slate-800 text-white rounded-xl text-xs font-black hover:bg-slate-900 transition shadow-md">
                    KEMBALI KE RIWAYAT
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .topbar, .flex.gap-2, .bg-slate-50 { display: none !important; }
    body { background: white !important; }
    .max-w-4xl { max-width: 100% !important; margin: 0 !important; }
    .rounded-3xl, .rounded-2xl { border-radius: 0 !important; border: none !important; }
    .shadow-sm, .shadow-md { box-shadow: none !important; }
}
</style>
@endsection