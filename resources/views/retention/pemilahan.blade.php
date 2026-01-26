@extends('layouts.app')
@section('title', 'Meja Pemilahan Berkas RM')

@section('content')
<div class="space-y-6 animate-in fade-in duration-700">
    
    <!-- HEADER: IDENTITAS TAHAPAN (SOP 3) -->
    <div class="bg-blue-600 p-8 rounded-3xl shadow-lg text-white relative overflow-hidden">
        <!-- Dekorasi Background -->
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
                <h2 class="text-3xl font-black uppercase tracking-tight leading-none">Meja Pemilahan Berkas</h2>
                <p class="text-blue-100 font-medium mt-2 max-w-xl text-sm italic">
                    Tahap audit fisik: Memilah lembar bernilai guna abadi sebelum berkas dipindahkan ke Gudang Inaktif.
                </p>
            </div>
            <div class="ml-auto flex flex-col items-center">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-1">Total Antrean</span>
                <div class="text-4xl font-black bg-white/20 px-6 py-2 rounded-2xl border border-white/30 shadow-inner">
                    {{ $patients->total() }}
                </div>
            </div>
        </div>
    </div>

    <!-- DATA TABLE SECTION -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-2 h-6 bg-blue-500 rounded-full"></div>
                <h3 class="font-black text-slate-700 text-xs uppercase tracking-widest">Daftar Berkas Dalam Tinjauan</h3>
            </div>
            <a href="{{ route('retensi.index') }}" class="px-4 py-2 bg-white text-slate-500 border border-slate-200 rounded-xl text-[10px] font-black hover:bg-slate-50 transition uppercase tracking-tighter shadow-sm flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b">
                    <tr>
                        <th class="px-8 py-5">Informasi Berkas (No RM)</th>
                        <th class="px-8 py-5">Kunjungan Terakhir</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        <th class="px-8 py-5 text-center">Aksi Peninjauan</th>
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
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black border border-amber-100 uppercase tracking-tighter animate-pulse">
                                Sedang Dipilah
                            </span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('patients.show', $p->id) }}" target="_blank" class="p-2.5 bg-white text-slate-400 hover:text-blue-600 border border-slate-200 rounded-xl transition shadow-sm group/btn" title="Tinjau Resume">
                                    <svg class="w-5 h-5 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                <form action="{{ route('retensi.pemilahan.selesai', $p->id) }}" method="POST" onsubmit="return confirm('Selesaikan pemilahan fisik?')">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-[11px] font-black hover:bg-emerald-700 transition shadow-lg uppercase active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                        Selesai
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-24 text-center">
                            <p class="text-slate-400 text-xs font-medium italic">Belum ada berkas yang diajukan untuk proses sortir fisik.</p>
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