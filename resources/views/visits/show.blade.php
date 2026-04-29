@extends('layouts.app')
@section('title', 'Lembar Kunjungan Medis')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-800 px-8 py-8 text-white flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
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
                    <p class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] mb-1">Nomor REKAM MEDIS</p>
                    <h1 class="text-3xl font-mono font-black tracking-tighter">{{ $visit->patient->no_rm }}</h1>
                </div>
            </div>
            
            <div class="relative z-10 text-right">
                <p class="text-sm font-bold opacity-80">{{ $visit->tgl_kunjungan->translatedFormat('l, d F Y') }}</p>
                <span class="inline-block mt-2 px-3 py-1 bg-emerald-500 rounded-lg text-[10px] font-black uppercase tracking-widest">Selesai Dilayani</span>
            </div>
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8 items-center bg-slate-50/50">
            <div class="md:col-span-2">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pasien Terdaftar</p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-lg border-2 border-white shadow-sm">
                        {{ substr($visit->patient->nama_pasien, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 uppercase">{{ $visit->patient->nama_pasien }}</h3>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <a href="{{ route('patients.show', $visit->patient->id) }}" class="flex items-center justify-center gap-2 py-3 bg-white text-emerald-700 border border-emerald-200 rounded-xl text-xs font-black hover:bg-emerald-50 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    LIHAT DETAIL DATA PASIEN
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center gap-2">
            <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
            <h3 class="font-black text-slate-700 text-xs uppercase tracking-widest">Detail Kunjungan</h3>
        </div>
        
        <div class="p-10 space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Poli Tujuan</label>
                    <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 flex items-center gap-3">
                        <div class="p-2 bg-blue-600 text-white rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span class="text-lg font-black text-blue-900 uppercase">{{ $visit->poli_tujuan }}</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode Pembayaran</label>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center gap-3">
                        <div class="p-2 bg-slate-700 text-white rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <span class="text-lg font-bold text-slate-800">{{ $visit->pembayaran ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kunjungan Terakhir Pasien</label>
                <div class="relative p-8 bg-amber-50 rounded-3xl border border-amber-100 italic text-amber-900 text-lg leading-relaxed shadow-inner">
                    <svg class="absolute top-4 left-4 w-8 h-8 text-amber-200" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                    <p class="relative z-10 pl-4 font-black text-2xl not-italic tracking-tighter">{{ $visit->patient->lastVisit ? $visit->patient->lastVisit->tgl_kunjungan->translatedFormat('d F Y') : $visit->tgl_kunjungan->translatedFormat('d F Y') }}</p>
                </div>
            </div>
        </div>

        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3 text-xs text-slate-400 font-bold uppercase tracking-wider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Dicatat oleh: <span class="text-slate-600">{{ $visit->user->nama_lengkap ?? 'Sistem' }}</span>
            </div>
            <div class="flex gap-2">
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