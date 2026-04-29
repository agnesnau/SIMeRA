@extends('layouts.app')
@section('title', 'Eksekusi Pemusnahan')

{{-- ================================================================= --}}
{{-- 1. DEKLARASI GLOBAL LEVEL USER --}}
{{-- ================================================================= --}}
@php
    $userLevel = auth()->check() ? strtolower(trim(auth()->user()->level)) : '';
    
    // Kenali Pimpinan / Kapuskesmas
    $kapusRoles = ['kapuskesmas', 'kepala', 'supervisor', 'pimpinan', 'kepala puskesmas'];
    $isKapus = in_array($userLevel, $kapusRoles);
    
    // Kenali Petugas
    $petugasRoles = ['petugas', 'admin', 'petugas_rm', 'rekam_medis'];
    $isPetugas = in_array($userLevel, $petugasRoles); 
@endphp

@section('content')
<div class="space-y-6 animate-in fade-in duration-700">

    {{-- NOTIFIKASI --}}
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
    
    {{-- MENAMPILKAN ERROR VALIDASI --}}
    @if ($errors->any())
    <div class="bg-red-50 text-red-800 p-4 rounded-xl border border-red-200 shadow-sm mt-4">
        <span class="font-bold">Gagal Diproses:</span>
        <ul class="list-disc ml-5 mt-1 text-sm font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
                    Perhatian: Berkas di halaman ini menunggu persetujuan. Pastikan Berita Acara Usulan sudah dicetak sebelum Kapuskesmas memberikan ACC.
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

    {{-- AREA TABEL DATA --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        
        {{-- HEADER TABEL & TOMBOL MASSAL --}}
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-2 h-6 bg-red-500 rounded-full"></div>
                <h3 class="font-black text-slate-700 text-xs uppercase tracking-widest">Daftar Berkas Usulan Pemusnahan</h3>
            </div>

            @php
                // =========================================================
                // PERBAIKAN FATAL: LOGIKA MENGHITUNG YANG BELUM ACC
                // =========================================================
                // Kita gunakan array kosong, lalu kita isi secara manual
                $idYangBelumAcc = [];
                $jumlahBelumAcc = 0;

                foreach($patients as $p) {
                    // Cek jika statusnya BUKAN 1 (Bukan angka 1, bukan string "1")
                    // DAN statusnya belum dieksekusi (bukan dimusnahkan)
                    if($p->status_approval != 1 && $p->manual_status != 'dimusnahkan') {
                        $idYangBelumAcc[] = $p->id;
                        $jumlahBelumAcc++;
                    }
                }
            @endphp

            @if($patients->count() > 0)
                <div class="flex gap-3">
                    {{-- 1. TOMBOL KAPUSKESMAS (HANYA ACC) --}}
                    @if($isKapus)
                        @if($jumlahBelumAcc > 0)
                            <form action="{{ route('eksekusi.approve') }}" method="POST">
                                @csrf
                                <input type="hidden" name="ids" value="{{ implode(',', $idYangBelumAcc) }}">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl text-[11px] font-black hover:bg-blue-700 transition shadow-lg shadow-blue-200 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Setujui (ACC) {{ $jumlahBelumAcc }} Berkas
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="px-6 py-2 bg-slate-200 text-slate-400 rounded-xl text-[11px] font-black cursor-not-allowed shadow-inner uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                Semua Sudah Di-ACC
                            </button>
                        @endif
                    @endif

                    {{-- 2. TOMBOL PETUGAS (CETAK MODAL) --}}
                    @if($isPetugas)
                        @if($jumlahBelumAcc > 0)
                            <button type="button" onclick="document.getElementById('modalUsulanMusnah').classList.remove('hidden')" class="px-6 py-2 bg-amber-500 text-white rounded-xl text-[11px] font-black hover:bg-amber-600 transition shadow-lg shadow-amber-200 uppercase tracking-wider flex items-center gap-2" title="Cetak Daftar Usulan Pemusnahan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Cetak Usulan
                            </button>
                        @endif
                    @endif
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b">
                    <tr>
                        <th class="px-8 py-5">Identitas Pasien</th>
                        <th class="px-8 py-5">Kunjungan Terakhir</th>
                        <th class="px-8 py-5 text-center">Status Persetujuan</th>
                        <th class="px-8 py-5 text-center">Aksi Eksekusi</th>
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
                            @if($p->manual_status === 'dimusnahkan')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black border border-emerald-100 uppercase tracking-tighter">
                                    Dimusnahkan
                                </span>
                            @elseif($p->status_approval == 1)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black border border-blue-100 uppercase tracking-tighter">
                                    Sudah Di-ACC
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black border border-amber-100 uppercase tracking-tighter">
                                    Menunggu ACC
                                </span>
                            @endif
                        </td>
                        
                        {{-- KOLOM AKSI EKSEKUSI (PER BARIS) --}}
                        <td class="px-8 py-6 text-center">
                            @if($p->manual_status === 'dimusnahkan')
                                {{-- 1. TAMPILAN JIKA SUDAH SELESAI DIMUSNAHKAN --}}
                                <div class="flex flex-col items-center gap-1">
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black border border-emerald-200 uppercase tracking-widest">
                                        Selesai Dimusnahkan
                                    </span>
                                    <small class="text-[9px] text-slate-400 italic">Siap dicetak di Laporan BA</small>
                                </div>

                            @elseif($p->status_approval == 1)
                                {{-- 2. TAMPILAN JIKA SUDAH DI-ACC (SIAP EKSEKUSI FORM) --}}
                                @if($isPetugas)
                                    <form action="{{ route('eksekusi.selesai', $p->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2 items-center w-48 mx-auto">
                                        @csrf
                                        <select name="status_nilai_guna" required class="w-full text-[10px] p-2 border border-slate-200 rounded-md bg-slate-50 font-bold"
                                            onchange="
                                                let area = document.getElementById('up_area_{{ $p->id }}');
                                                let inp = document.getElementById('file_{{ $p->id }}');
                                                if(this.value === 'ada') { area.style.display = 'block'; inp.required = true; }
                                                else { area.style.display = 'none'; inp.required = false; }
                                            ">
                                            <option value="" disabled selected>-- Ada Nilai Guna? --</option>
                                            <option value="ada">Ada (Wajib Upload)</option>
                                            <option value="tidak">Tidak Ada</option>
                                        </select>

                                        <div id="up_area_{{ $p->id }}" style="display: none;" class="w-full">
                                            <input type="file" id="file_{{ $p->id }}" name="file_nilai_guna" class="w-full text-[10px] text-slate-500">
                                        </div>

                                        <button type="submit" class="w-full px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-[10px] font-black hover:bg-emerald-600 transition shadow-md">
                                            Selesaikan Eksekusi
                                        </button>
                                    </form>

                                    {{-- TOMBOL KOREKSI FISIK RAK --}}
                                    <form action="{{ route('eksekusi.koreksi', $p->id) }}" method="POST" onsubmit="return confirm('KOREKSI: Kembalikan berkas ini ke Gudang Inaktif? (Membatalkan ACC)')" class="mt-3">
                                        @csrf
                                        <button type="submit" class="text-[9px] font-bold text-red-500 hover:text-red-700 underline decoration-dotted transition flex items-center justify-center gap-1 mx-auto w-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Koreksi (Batal Eksekusi)
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400">Menunggu Eksekusi Petugas</span>
                                @endif

                            @else
                                {{-- 3. TAMPILAN JIKA BELUM ACC (TERKUNCI + TOMBOL BATAL USUL) --}}
                                <div class="flex flex-col items-center gap-2">
                                    @if($isKapus)
                                        <span class="px-4 py-1.5 bg-amber-100 text-amber-700 rounded-lg text-[10px] font-black border border-amber-200">
                                            Menunggu ACC Anda ☝️
                                        </span>
                                    @else
                                        <span class="px-4 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-[10px] font-black border border-slate-200 flex items-center gap-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            Terkunci (Butuh ACC)
                                        </span>
                                        
                                        <form action="{{ route('eksekusi.batal', $p->id) }}" method="POST" onsubmit="return confirm('Kembalikan berkas ini ke Gudang Inaktif?')">
                                            @csrf
                                            <button type="submit" class="text-[9px] font-bold text-red-400 hover:text-red-600 underline decoration-dotted transition">
                                                Batal Usul Musnah
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-slate-400 text-sm font-medium italic">Tidak ada berkas yang menunggu pemusnahan.</p>
                                <p class="text-slate-300 text-xs">Gunakan aksi Usul Musnah dari Daftar Utama terlebih dahulu.</p>
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

{{-- MODAL POP-UP ISI DATA USULAN PEMUSNAHAN --}}
<div id="modalUsulanMusnah" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-lg relative animate-in zoom-in-95 duration-300">
        
        <button type="button" onclick="document.getElementById('modalUsulanMusnah').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-red-500 bg-slate-100 p-2 rounded-full transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-1">Lengkapi Data Usulan Pemusnahan</h3>
        <p class="text-xs text-slate-500 font-medium mb-6">Silakan isi identitas penandatangan sebelum mencetak formulir usulan.</p>

        <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank" class="space-y-4">
            @csrf
            <input type="hidden" name="jenis_ba" value="pemusnahan"> 
            <input type="hidden" name="tipe_dokumen" value="pertelaan">
            <input type="hidden" name="sk_kapus" value="-">
            <input type="hidden" name="metode" value="-">
            <input type="hidden" name="lokasi" value="-">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">No Surat Usulan</label>
                    <input type="text" name="no_surat" value="000/USUL-PM/RM/{{ date('Y') }}" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">Tanggal Cetak</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" required>
                </div>
            </div>

            <div class="grid grid-cols-5 gap-3">
                <div class="col-span-3">
                    <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Ketua Tim (Pengusul)</label>
                    <input type="text" name="nama_ketua" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" required placeholder="Nama Lengkap Ketua Tim">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">NIP Ketua</label>
                    <input type="text" name="nip_ketua" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" placeholder="NIP / -">
                </div>
            </div>

            <div class="grid grid-cols-5 gap-3">
                <div class="col-span-3">
                    <label class="block text-[10px] font-black text-slate-800 uppercase tracking-widest mb-1">Kepala Puskesmas (Penyetuju)</label>
                    <input type="text" name="nama_kapus" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" required placeholder="Nama Lengkap Kapus">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-800 uppercase tracking-widest mb-1">NIP Kapus</label>
                    <input type="text" name="nip_kapus" class="w-full border-2 border-slate-200 p-2.5 text-sm font-bold rounded-xl focus:border-amber-500 outline-none" placeholder="NIP / -">
                </div>
            </div>

            <button type="submit" onclick="document.getElementById('modalUsulanMusnah').classList.add('hidden')" class="w-full py-3 mt-4 bg-amber-500 text-white rounded-xl font-black uppercase tracking-[0.2em] shadow-lg shadow-amber-200 hover:bg-amber-600 active:scale-95 transition-all">
                Cetak File Usulan
            </button>
        </form>
    </div>
</div>
@endsection