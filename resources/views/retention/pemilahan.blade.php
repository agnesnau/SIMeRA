@extends('layouts.app')
@section('title', 'Pemilahan Berkas RM')

@section('content')
<div class="space-y-6 animate-in fade-in duration-700">

    {{-- BAGIAN NOTIFIKASI --}}
    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl border border-emerald-200 flex items-center gap-3 shadow-sm">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 text-red-800 p-4 rounded-xl border border-red-200 flex items-center gap-3 shadow-sm">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-bold">{{ session('error') }}</span>
    </div>
    @endif

    {{-- BAGIAN HEADER --}}
    <div class="bg-blue-600 p-8 rounded-3xl shadow-lg text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
            <svg width="250" height="250" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14h-2V9h-2V7h4v10z"/>
            </svg>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-md border border-white/30 shadow-2xl">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 022 2h2a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-black uppercase tracking-tight leading-none">Meja Pemilahan Berkas</h2>
                <p class="text-blue-100 font-medium mt-2 max-w-xl text-sm italic">
                    Tahap audit fisik: Menunggu Persetujuan Kapuskesmas sebelum dipindahkan ke Gudang Inaktif.
                </p>
            </div>
            <div class="ml-auto flex flex-col items-center">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-1">Total Usulan</span>
                <div class="text-4xl font-black bg-white/20 px-6 py-2 rounded-2xl border border-white/30 shadow-inner">
                    {{ $patients->total() }}
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mt-6">
        
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
                    {{-- VARIABEL LEVEL USER & PENGHITUNG STATUS --}}
                    @php
                        $userLevel = trim(strtolower(auth()->user()->level ?? ''));
                        $isKapus = in_array($userLevel, ['kapuskesmas', 'kepala', 'supervisor']);
                        $isPetugas = in_array($userLevel, ['petugas', 'admin', 'petugas_rm']); 

                        $belumAcc = $patients->where('status_approval', 0);
                        $sudahAcc = $patients->where('status_approval', 1);
                    @endphp

                    {{-- 1. TOMBOL KHUSUS KAPUSKESMAS (HANYA ACC) --}}
                    @if($isKapus)
                        @if($belumAcc->count() > 0)
                            <form action="{{ route('retensi.pemilahan.approve') }}" method="POST">
                                @csrf
                                <input type="hidden" name="ids" value="{{ $belumAcc->pluck('id')->implode(',') }}">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl text-[11px] font-black hover:bg-blue-700 transition shadow-lg shadow-blue-200 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Setujui (ACC) Semua
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="px-6 py-2 bg-slate-200 text-slate-400 rounded-xl text-[11px] font-black cursor-not-allowed shadow-inner uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                Semua Sudah Di-ACC
                            </button>
                        @endif
                    @endif

                    {{-- 2. TOMBOL KHUSUS PETUGAS (CETAK USULAN & PINDAH GUDANG MASSAL) --}}
                    @if($isPetugas)
                        
                        {{-- Tombol Cetak Usulan Retensi --}}
                        @if($belumAcc->count() > 0)
                            <button type="button" onclick="document.getElementById('modalUsulanRetensi').classList.remove('hidden')" class="px-6 py-2 bg-amber-500 text-white rounded-xl text-[11px] font-black hover:bg-amber-600 transition shadow-lg shadow-amber-200 uppercase tracking-wider flex items-center gap-2" title="Cetak Daftar Usulan Retensi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Cetak Usulan
                            </button>
                        @endif

                        {{-- Tombol Pindah Gudang (Massal) --}}
                        <form action="{{ route('sorting.finishAll') }}" method="POST" onsubmit="return confirm('KONFIRMASI: Apakah Anda yakin memindahkan berkas yang DISETUJUI ini ke Gudang Inaktif?')">
                            @csrf
                            <button type="submit" 
                                class="px-6 py-2 {{ $sudahAcc->count() > 0 ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200 animate-pulse text-white' : 'bg-slate-300 text-slate-500 cursor-not-allowed' }} rounded-xl text-[11px] font-black transition shadow-lg uppercase tracking-wider flex items-center gap-2"
                                {{ $sudahAcc->count() == 0 ? 'disabled title="Menunggu ACC Kapuskesmas"' : '' }}>
                                
                                @if($sudahAcc->count() == 0)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Terkunci
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    Selesai (Masuk Gudang)
                                @endif
                            </button>
                        </form>
                    @endif

                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b">
                    <tr>
                        <th class="px-8 py-5">Informasi Berkas (No RM)</th>
                        <th class="px-8 py-5">Kunjungan Terakhir</th>
                        <th class="px-8 py-5 text-center">Status Persetujuan</th>
                        <th class="px-8 py-5 text-center">Aksi Pemilahan</th>
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
                            @if($p->status_approval == 1)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black border border-blue-100 uppercase tracking-tighter">
                                    Sudah Di-ACC
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black border border-amber-100 uppercase tracking-tighter">
                                    Menunggu ACC
                                </span>
                            @endif
                        </td>

                        {{-- KOLOM AKSI (BATAL USUL & KOREKSI) --}}
                        <td class="px-8 py-6 text-center">
                            @if(isset($isPetugas) && $isPetugas)
                                @if($p->status_approval == 1)
                                    {{-- TAMPILAN JIKA SUDAH ACC (SIAP MASUK GUDANG ATAU KOREKSI FISIK) --}}
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black border border-emerald-200 uppercase tracking-widest">
                                            Siap Masuk Gudang
                                        </span>
                                        
                                        <form action="{{ route('pemilahan.koreksi', $p->id) }}" method="POST" onsubmit="return confirm('KOREKSI: Batalkan ACC dan kembalikan status pasien menjadi AKTIF berobat?')">
                                            @csrf
                                            <button type="submit" class="text-[9px] font-bold text-red-500 hover:text-red-700 underline decoration-dotted transition flex items-center justify-center gap-1 mx-auto w-full mt-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Koreksi (Batal)
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    {{-- TAMPILAN JIKA BELUM ACC (TERKUNCI + BATAL USUL) --}}
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="px-4 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-[10px] font-black border border-slate-200 flex items-center gap-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            Terkunci
                                        </span>
                                        <form action="{{ route('pemilahan.batal', $p->id) }}" method="POST" onsubmit="return confirm('Batalkan usulan? Berkas ini akan ditarik kembali menjadi status AKTIF.')">
                                            @csrf
                                            <button type="submit" class="text-[9px] font-bold text-red-400 hover:text-red-600 underline decoration-dotted transition">
                                                Batal Usul Retensi
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @else
                                {{-- JIKA KAPUSKESMAS YG MELIHAT --}}
                                @if($p->status_approval == 1)
                                    <span class="text-[10px] font-bold text-slate-400">Menunggu Eksekusi Petugas</span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400">Menunggu ACC Anda</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-24 text-center">
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

{{-- MODAL POP-UP ISI DATA USULAN RETENSI (DENGAN NIP LENGKAP) --}}
<div id="modalUsulanRetensi" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-lg relative animate-in zoom-in-95 duration-300">
        
        <button type="button" onclick="document.getElementById('modalUsulanRetensi').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-red-500 bg-slate-100 p-2 rounded-full transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-1">Lengkapi Data Usulan Retensi</h3>
        <p class="text-xs text-slate-500 font-medium mb-6">Silakan isi identitas penandatangan sebelum mencetak formulir usulan.</p>

        <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank" class="space-y-4">
            @csrf
            <input type="hidden" name="jenis_ba" value="retensi"> 
            <input type="hidden" name="tipe_dokumen" value="pertelaan">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">No Surat Usulan</label>
                    <input type="text" name="no_surat" value="000/USUL-RET/RM/{{ date('Y') }}" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">Tanggal Cetak</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" required>
                </div>
            </div>

            <div class="grid grid-cols-5 gap-3">
                <div class="col-span-3">
                    <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">PJ RM (Pengusul)</label>
                    <input type="text" name="nama_p1" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" required placeholder="Nama Lengkap">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">NIP PJ RM</label>
                    <input type="text" name="nip_p1" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" placeholder="NIP / -">
                </div>
            </div>

            <div class="grid grid-cols-5 gap-3">
                <div class="col-span-3">
                    <label class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Kasubag TU (Saksi)</label>
                    <input type="text" name="nama_p2" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" required placeholder="Nama Lengkap">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">NIP Kasubag TU</label>
                    <input type="text" name="nip_p2" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" placeholder="NIP / -">
                </div>
            </div>

            <div class="grid grid-cols-5 gap-3">
                <div class="col-span-3">
                    <label class="block text-[10px] font-black text-slate-800 uppercase tracking-widest mb-1">Kepala Puskesmas</label>
                    <input type="text" name="nama_tu" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" required placeholder="Nama Lengkap">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-800 uppercase tracking-widest mb-1">NIP Kapus</label>
                    <input type="text" name="nip_tu" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" placeholder="NIP / -">
                </div>
            </div>

            <button type="submit" onclick="document.getElementById('modalUsulanRetensi').classList.add('hidden')" class="w-full py-3 mt-4 bg-amber-500 text-white rounded-xl font-black uppercase tracking-[0.2em] shadow-lg shadow-amber-200 hover:bg-amber-600 active:scale-95 transition-all">
                Cetak File Usulan
            </button>
        </form>
    </div>
</div>

@endsection