@extends('layouts.app')
@section('title', 'Pemilahan Berkas RM')

@section('content')
<div class="space-y-6 animate-in fade-in duration-700">
    

{{-- =============================================== --}}
    {{-- 1. BAGIAN NOTIFIKASI (ALERT)                    --}}
    {{-- =============================================== --}}
    
    @if(session('success'))
    <div id="alert-success" class="flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-sm" role="alert">
        <svg class="flex-shrink-0 w-5 h-5 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
        <div class="text-sm font-bold">
            {{ session('success') }}
        </div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-emerald-50 text-emerald-500 rounded-lg focus:ring-2 focus:ring-emerald-400 p-1.5 hover:bg-emerald-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-success" aria-label="Close" onclick="this.parentElement.remove()">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div id="alert-error" class="flex items-center p-4 mb-4 text-red-800 rounded-xl bg-red-50 border border-red-200 shadow-sm" role="alert">
        <svg class="flex-shrink-0 w-5 h-5 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
        </svg>
        <div class="text-sm font-bold">
            {{ session('error') }}
        </div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-error" aria-label="Close" onclick="this.parentElement.remove()">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- =============================================== --}}
    {{-- 2. BAGIAN HEADER (SOP 3)                        --}}
    {{-- =============================================== --}}
    <div class="bg-blue-600 p-8 rounded-3xl shadow-lg text-white relative overflow-hidden">
        {{-- ... Kode header Anda biarkan di sini ... --}}
    <div class="bg-blue-600 p-8 rounded-3xl shadow-lg text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
            <svg width="250" height="250" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14h-2V9h-2V7h4v10z"/>
            </svg>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-md border border-white/30 shadow-2xl">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 022 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-black uppercase tracking-tight leading-none">Pemilahan Berkas Rekam Medis</h2>
                <p class="text-blue-100 font-medium mt-2 max-w-xl text-sm italic">
                    Tahap audit fisik: Memilah berkas berusia >2 thn untuk dipindahkan ke Gudang Inaktif.
                </p>
            </div>
            <div class="ml-auto flex flex-col items-center">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-1">Total</span>
                <div class="text-4xl font-black bg-white/20 px-6 py-2 rounded-2xl border border-white/30 shadow-inner">
                    {{ $patients->total() }}
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-2 h-6 bg-blue-500 rounded-full"></div>
                <h3 class="font-black text-slate-700 text-xs uppercase tracking-widest">Daftar Berkas Dalam Tinjauan</h3>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('retensi.index') }}" class="px-4 py-2 bg-white text-slate-500 border border-slate-200 rounded-xl text-[10px] font-black hover:bg-slate-50 transition uppercase tracking-tighter shadow-sm flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>

                @if($patients->count() > 0)
                <form action="{{ route('sorting.finishAll') }}" method="POST" onsubmit="return confirm('KONFIRMASI: Apakah Anda yakin seluruh berkas dalam tabel ini SUDAH DIPILAH dan siap masuk gudang inaktif? Data akan hilang dari halaman ini.')">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-xl text-[11px] font-black hover:bg-emerald-700 transition shadow-lg uppercase tracking-wider flex items-center gap-2 animate-pulse">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        Selesai (Simpan Semua ke Gudang)
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b">
                    <tr>
                        <th class="px-8 py-5">Informasi Berkas (No RM)</th>
                        <th class="px-8 py-5">Kunjungan Terakhir</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $p)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="font-black text-slate-800 uppercase text-base">{{ $p->nama_pasien }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-md font-mono font-bold text-[11px] tracking-tighter">INDEX: {{ $p->no_rm }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="font-bold text-slate-600">{{ $p->lastVisit ? $p->lastVisit->tgl_kunjungan->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Puskesmas Silo 1</div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black border border-amber-100 uppercase tracking-tighter">
                                Sedang Dipilah
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="text-slate-400 text-sm font-medium italic">Meja pemilahan bersih. Tidak ada berkas yang perlu ditinjau.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
        <div class="p-6 border-t border-slate-100 bg-slate-50/30">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection