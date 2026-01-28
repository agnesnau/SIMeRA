@extends('layouts.app')
@section('title', 'Pelaporan & Berita Acara')

@section('content')
<!-- Container Utama: Ukuran max-w-5xl yang lebih standar -->
<div class="max-w-5xl mx-auto px-4 pb-16 space-y-8 animate-in fade-in duration-700" x-data="{ tab: 'retensi' }">

    <!-- 1. HEADER: LEBIH RINGKAS & PROFESIONAL -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-black uppercase tracking-widest rounded-full">Reporting</span>
                <div class="h-0.5 w-8 bg-slate-200 rounded-full"></div>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Manajemen Pelaporan</h1>
            <p class="text-slate-500 text-sm font-medium mt-0.5">Dokumen legalitas retensi dan pemusnahan rekam medis.</p>
        </div>
        
        <div class="flex gap-3">
            <!-- Statistik Bergaya Badge -->
            <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="w-1.5 h-6 bg-emerald-500 rounded-full"></div>
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Inaktif</p>
                    <p class="text-base font-black text-slate-800 leading-none mt-1">{{ $totalInaktif ?? 0 }}</p>
                </div>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="w-1.5 h-6 bg-red-500 rounded-full"></div>
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Musnah</p>
                    <p class="text-base font-black text-slate-800 leading-none mt-1">{{ $siapMusnah ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. NAVIGASI TAB: LEBIH PADAT -->
    <div class="flex justify-center">
        <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 shadow-inner">
            <button @click="tab = 'retensi'" 
                :class="tab === 'retensi' ? 'bg-white text-emerald-800 shadow-sm scale-100' : 'text-slate-500 hover:text-slate-800'"
                class="px-6 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                BA Retensi
            </button>
            <button @click="tab = 'pemusnahan'" 
                :class="tab === 'pemusnahan' ? 'bg-white text-red-800 shadow-sm scale-100' : 'text-slate-500 hover:text-slate-800'"
                class="px-6 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                BA Pemusnahan
            </button>
            <button @click="tab = 'riwayat'" 
                :class="tab === 'riwayat' ? 'bg-slate-800 text-white shadow-md scale-100' : 'text-slate-500 hover:text-slate-800'"
                class="px-6 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Riwayat
            </button>
        </div>
    </div>

    <!-- 3. KONTEN FORMULIR: SKALA STANDAR -->
    <div class="relative min-h-[500px]">
        
        <!-- FORM BERITA ACARA RETENSI -->
        <div x-show="tab === 'retensi'" x-cloak class="bg-white rounded-2xl shadow-lg shadow-slate-200/40 border border-slate-100 overflow-hidden animate-in zoom-in-95 duration-500">
            <div class="bg-emerald-600 px-8 py-5 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black uppercase tracking-tight">Berita Acara Retensi</h3>
                    <p class="text-emerald-100 text-[9px] font-bold uppercase tracking-widest">Kegiatan Pemilahan Berkas Rekam Medis Inaktif</p>
                </div>
                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>

            <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank" class="p-8 space-y-8">
                @csrf
                <input type="hidden" name="jenis_ba" value="retensi">
                
                <!-- Administrasi Surat -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nomor Surat</label>
                        <input type="text" name="no_surat" value="000/BA-RET/RM/{{ date('Y') }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Tanggal BA</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1.5 italic">Rentang Tahun</label>
                        <input type="text" name="rentang_tahun" placeholder="Contoh: 2021 - 2023" class="w-full bg-amber-50 border-amber-100 p-3 rounded-xl text-sm font-bold text-amber-900 focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                </div>

                <!-- Tanda Tangan -->
                <div class="pt-6 border-t border-slate-100">
                    <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span class="w-6 h-6 bg-slate-900 text-white rounded-md flex items-center justify-center text-[10px]">!</span>
                        Pejabat Bertanda Tangan
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest">PJ Rekam Medis</label>
                            <input type="text" name="nama_p1" placeholder="Nama PJ RM" class="w-full border-slate-200 p-3 rounded-xl text-sm outline-none focus:ring-2 focus:ring-emerald-500" required>
                            <input type="text" name="nip_p1" placeholder="NIP/NIK" class="w-full border-slate-200 p-3 rounded-xl text-xs outline-none">
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-blue-600 uppercase tracking-widest">Kasubag TU</label>
                            <input type="text" name="nama_p2" placeholder="Nama Kasubag TU" class="w-full border-slate-200 p-3 rounded-xl text-sm outline-none focus:ring-2 focus:ring-emerald-500" required>
                            <input type="text" name="nip_p2" placeholder="NIP/NIK" class="w-full border-slate-200 p-3 rounded-xl text-xs outline-none">
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-800 uppercase tracking-widest">Kepala Puskesmas</label>
                            <input type="text" name="nama_tu" placeholder="Nama Kepala Puskesmas" class="w-full border-2 border-slate-300 p-3 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500" required>
                            <input type="text" name="nip_tu" placeholder="NIP Kapus" class="w-full border-slate-200 p-3 rounded-xl text-xs outline-none">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-xl font-black shadow-lg hover:bg-emerald-600 transition-all uppercase text-[10px] tracking-[0.3em] active:scale-95 flex items-center justify-center gap-3">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    CETAK DOKUMEN RETENSI
                </button>
            </form>
        </div>

        <!-- FORM BERITA ACARA PEMUSNAHAN -->
        <div x-show="tab === 'pemusnahan'" x-cloak class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden animate-in slide-in-from-bottom-6 duration-500">
             <div class="bg-red-600 px-8 py-5 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black uppercase tracking-tight">Berita Acara Pemusnahan</h3>
                    <p class="text-red-100 text-[9px] font-bold uppercase tracking-widest">Kegiatan Pemusnahan Fisik Berkas Rekam Medis > 4 Thn</p>
                </div>
                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
            </div>

            <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank" class="p-8 space-y-8">
                @csrf
                <input type="hidden" name="jenis_ba" value="pemusnahan">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="col-span-full bg-red-50/50 p-6 rounded-2xl border border-red-100 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-red-500 uppercase tracking-widest">No Surat BAP</label>
                                <input type="text" name="no_surat" value="000/BA-PM/RM/{{ date('Y') }}" class="w-full border-slate-200 p-3 rounded-xl text-sm font-black outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-red-500 uppercase tracking-widest">Tanggal Eksekusi</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border-slate-200 p-3 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-red-500 uppercase tracking-widest">Dasar SK Kapus</label>
                            <input type="text" name="sk_kapus" placeholder="No SK Tim Pemusnah" class="w-full border-slate-200 p-3 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-red-500" required>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest">Tim Pelaksana</label>
                        <input type="text" name="nama_ketua" placeholder="Nama Ketua (PJ RM)" class="w-full border border-slate-200 p-3 rounded-xl text-sm font-bold" required>
                        <input type="text" name="nama_kapus" placeholder="Nama Kepala Puskesmas" class="w-full border border-slate-200 p-3 rounded-xl text-sm font-bold" required>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest">Metode & Lokasi</label>
                        <input type="text" name="metode" value="Dicacah dan dihancurkan" class="w-full border border-slate-200 p-3 rounded-xl text-sm font-bold" required>
                        <input type="text" name="lokasi" value="Halaman Belakang Puskesmas" class="w-full border border-slate-200 p-3 rounded-xl text-sm font-bold" required>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-red-600 text-white rounded-xl font-black shadow-lg hover:bg-red-700 transition-all uppercase text-[10px] tracking-[0.3em] active:scale-95 flex items-center justify-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    CETAK LAPORAN PEMUSNAHAN
                </button>
            </form>
        </div>

        <!-- TAB 3: RIWAYAT ARSIP -->
        <div x-show="tab === 'riwayat'" x-cloak class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-500">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Arsip Digital Laporan</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-0.5">Daftar Berita Acara Resmi</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center border border-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] border-b">
                        <tr>
                            <th class="px-8 py-4">Tgl / No Surat</th>
                            <th class="px-8 py-4 text-center">Jenis</th>
                            <th class="px-8 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($history as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-8 py-5">
                                <div class="font-black text-slate-700 uppercase text-sm leading-none">{{ $item->tanggal_ba->format('d M Y') }}</div>
                                <div class="text-[10px] font-mono text-blue-600 font-bold mt-1 uppercase">{{ $item->no_surat }}</div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($item->jenis_ba == 'retensi')
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[9px] font-black border border-emerald-100 uppercase tracking-tighter">RETENSI</span>
                                @else
                                    <span class="px-3 py-1 bg-red-50 text-red-700 rounded-full text-[9px] font-black border border-red-100 uppercase tracking-tighter">PEMUSNAHAN</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                <a href="{{ route('laporan.reprint', $item->id) }}" target="_blank" class="px-4 py-2 bg-white text-slate-600 border border-slate-200 rounded-lg text-[9px] font-black hover:bg-blue-600 hover:text-white transition-all shadow-sm uppercase tracking-widest active:scale-95 inline-flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak Ulang
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-20 text-center text-slate-300 font-black uppercase italic tracking-widest text-[10px]">
                                Belum ada arsip laporan digital.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection