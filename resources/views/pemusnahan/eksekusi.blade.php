@extends('layouts.app')
@section('title', 'Eksekusi Pemusnahan')

@section('content')
<div class="space-y-6 animate-in fade-in duration-700">

    {{-- NOTIFIKASI --}}
    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl border border-emerald-200 flex items-center gap-3 shadow-sm">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- HEADER MERAH (ZONA PEMUSNAHAN) --}}
    <div class="bg-red-700 p-8 rounded-3xl shadow-lg text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
            <svg width="250" height="250" fill="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-md border border-white/30 shadow-2xl">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-black uppercase tracking-tight leading-none">Area Eksekusi Pemusnahan</h2>
                <p class="text-red-100 font-medium mt-2 max-w-xl text-sm italic">
                    Perhatian: Berkas di halaman ini akan dihapus statusnya secara permanen (Dimusnahkan). Pastikan Berita Acara sudah dicetak.
                </p>
            </div>
            <div class="ml-auto flex flex-col items-center">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-1">Siap Musnah</span>
                <div class="text-4xl font-black bg-white/20 px-6 py-2 rounded-2xl border border-white/30 shadow-inner">
                    {{ $patients->total() }}
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-2 h-6 bg-red-500 rounded-full"></div>
                <h3 class="font-black text-slate-700 text-xs uppercase tracking-widest">Daftar Berkas Siap Eksekusi</h3>
            </div>

            @if($patients->count() > 0)
            <form action="{{ route('pemusnahan.executeAll') }}" method="POST" onsubmit="return confirm('PERINGATAN KERAS:\n\nAnda akan memusnahkan {{ $patients->total() }} berkas rekam medis.\nData status akan diubah menjadi DIMUSNAHKAN.\n\nLanjutkan eksekusi?')">
                @csrf
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl text-[11px] font-black hover:bg-red-700 transition shadow-lg shadow-red-200 uppercase tracking-wider flex items-center gap-2 animate-pulse">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Selesai (Musnahkan Semua)
                </button>
            </form>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b">
                    <tr>
                        <th class="px-8 py-5">Identitas Pasien</th>
                        <th class="px-8 py-5">Kunjungan Terakhir</th>
                        <th class="px-8 py-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $p)
                    <tr class="hover:bg-red-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="font-black text-slate-800 uppercase text-base">{{ $p->nama_pasien }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md font-mono font-bold text-[11px] tracking-tighter">RM: {{ $p->no_rm }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="font-bold text-slate-600">{{ $p->lastVisit ? $p->lastVisit->tgl_kunjungan->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                {{ $p->lastVisit ? $p->lastVisit->tgl_kunjungan->diffForHumans() : '' }}
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black border border-red-100 uppercase tracking-tighter">
                                Siap Eksekusi
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-slate-400 text-sm font-medium italic">Tidak ada berkas yang menunggu pemusnahan.</p>
                                <p class="text-slate-300 text-xs">Pindahkan data dari menu Retensi RM terlebih dahulu.</p>
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