@extends('layouts.app')
@section('title', 'Pelaporan & Berita Acara Digital')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ tab: 'retensi' }" x-init="console.log('Alpine initialized')" x-cloak>

    <!-- 1. STATISTIK RINGKAS (Mencegah Error dengan Operator ??) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-sm transition hover:shadow-md">
            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Kandidat Inaktif</p>
            <h4 class="text-xl font-bold text-emerald-600">{{ $totalInaktif ?? 0 }} Berkas</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-red-100 shadow-sm transition hover:shadow-md">
            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Kandidat Musnah</p>
            <h4 class="text-xl font-bold text-red-600">{{ $siapMusnah ?? 0 }} Berkas</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-gray-400 transition hover:shadow-md">
            <p class="text-[10px] uppercase font-bold tracking-wider">Total Dimusnahkan</p>
            <h4 class="text-xl font-bold">{{ $sudahMusnah ?? 0 }} Berkas</h4>
        </div>
    </div>

    <!-- 2. TAB PILIHAN (Beralih antara Retensi & Pemusnahan) -->
    <div class="flex p-1 bg-gray-200 rounded-xl shadow-inner">
        <button @click="tab = 'retensi'" 
            :class="tab === 'retensi' ? 'bg-white shadow text-emerald-700' : 'text-gray-600 hover:text-emerald-500'" 
            class="flex-1 py-3 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            BA Retensi (Penyusutan)
        </button>
        <button @click="tab = 'pemusnahan'" 
            :class="tab === 'pemusnahan' ? 'bg-white shadow text-red-700' : 'text-gray-600 hover:text-red-500'" 
            class="flex-1 py-3 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            BA Pemusnahan (Eksekusi)
        </button>
    </div>

    <!-- 3. FORM BA RETENSI (PENYUSUTAN) -->
    <div x-show="tab === 'retensi'" x-transition:enter="transition ease-out duration-300" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <svg class="w-24 h-24 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h.01a1 1 0 100-2H10zm3 0a1 1 0 000 2h.01a1 1 0 100-2H13zM7 13a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h.01a1 1 0 100-2H10zm3 0a1 1 0 000 2h.01a1 1 0 100-2H13z" clip-rule="evenodd"></path></svg>
        </div>
        
        <div class="mb-6 border-b pb-4 relative z-10">
            <h4 class="font-bold text-gray-800 text-lg">Form Berita Acara Retensi</h4>
            <p class="text-xs text-emerald-600 font-medium uppercase tracking-wider">Pemilahan Arsip rekam Medis yang Telah Memasuki Masa Inaktif (>2 Tahun)</p>
        </div>

        <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank" class="space-y-6">
            @csrf
            <input type="hidden" name="jenis_ba" value="retensi">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Informasi Surat & Tanggal</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="no_surat" value="000/BA-ST/RM/{{ date('Y') }}" class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none" required placeholder="Nomor Surat">
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                </div>

                <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Rentang Tahun Kunjungan Pasien</label>
                        <input type="text" name="rentang_tahun" placeholder="Contoh: 2017 - 2019" class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Total Arsip</label>
                        <input type="number" name="total_berkas" value="{{ $totalInaktif ?? 0 }}" class="w-full border p-2.5 rounded-lg text-sm bg-gray-50 font-bold outline-none" required>
                    </div>
                </div>

                <div class="border-t pt-4 col-span-2">
                    <p class="text-xs font-bold text-emerald-600 mb-4 uppercase tracking-widest"></p>
                </div>

                <!-- Pihak I -->
                <div class="space-y-3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Petugas Pelaksana</label>
                    <input type="text" name="nama_p1" placeholder="Nama Petugas Pelaksana" class="w-full border p-2.5 rounded-lg text-sm mb-2" required>
                    <input type="text" name="nip_p1" placeholder="NIP / NIK" class="w-full border p-2.5 rounded-lg text-sm">
                    <input type="hidden" name="jabatan_p1" value="Petugas Rekam Medis">
                </div>

                <!-- Pihak II -->
                <div class="space-y-3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Penangggung Jawab Rekam Medis</label>
                    <input type="text" name="nama_p2" placeholder="Nama PJ Rekam Medis" class="w-full border p-2.5 rounded-lg text-sm mb-2" required>
                    <input type="text" name="nip_p2" placeholder="NIP / NIK" class="w-full border p-2.5 rounded-lg text-sm">
                    <input type="hidden" name="jabatan_p2" value="Penanggung Jawab Ruang Inaktif">
                </div>

                <!-- Mengetahui TU -->
                <div class="col-span-2 space-y-3 bg-emerald-50 p-5 rounded-xl border border-emerald-100 shadow-inner">
                    <label class="block text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Mengetahui (Kepala Puskesmas)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="nama_tu" placeholder="Nama Lengkap Kepala Puskesmas" class="w-full border p-2.5 rounded-lg text-sm bg-white" required>
                        <input type="text" name="nip_tu" placeholder="NIP Kepala Puskesmas" class="w-full border p-2.5 rounded-lg text-sm bg-white">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                CETAK BERITA ACARA RETENSI REKAM MEDIS
            </button>
        </form>
    </div>

    <!-- 4. FORM BA PEMUSNAHAN (EKSEKUSI) -->
    <div x-show="tab === 'pemusnahan'" x-transition:enter="transition ease-out duration-300" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <svg class="w-24 h-24 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
        </div>

        <div class="mb-6 border-b pb-4 relative z-10">
            <h4 class="font-bold text-red-800 text-lg">Form Berita Acara Pemusnahan</h4>
            <p class="text-xs text-red-600 font-medium uppercase tracking-wider">Penghancuran Arsip Rekam Medis yang Tidak Memiliki Nilai Guna Abadi (> 4 Tahun)</p>
        </div>

        <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank" class="space-y-6">
            @csrf
            <input type="hidden" name="jenis_ba" value="pemusnahan">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Parameter Surat & Eksekusi</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="no_surat" value="000/BA-PM/RM/{{ date('Y') }}" class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none" required placeholder="Nomor Surat">
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none" required>
                    </div>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Dasar Pelaksanaan (Nomor SK Kepala Puskesmas)</label>
                    <input type="text" name="sk_kapus" placeholder="Contoh: SK/001/PKM-SILO1/2026" class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none" required>
                </div>

                <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Metode Pemusnahan</label>
                        <select name="metode" class="w-full border p-2.5 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 outline-none">
                            <option value="Dibakar dengan Incinerator Puskesmas">Dibakar dengan Incinerator</option>
                            <option value="Dicacah menggunakan mesin pencacah kertas">Dicacah (Shredding)</option>
                            <option value="Dikirim ke Pihak Ketiga pengolah limbah B3">Pihak Ketiga (Limbah B3)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi Eksekusi</label>
                        <input type="text" name="lokasi" value="{{ $lokasi ?? 'Puskesmas Silo 1' }}" class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none" required>
                    </div>
                </div>

                <div class="border-t pt-4 col-span-2">
                    <p class="text-xs font-bold text-red-600 mb-4 uppercase tracking-widest">Tim Pemusnahan Rekam Medis</p>
                </div>

                <div class="space-y-3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Ketua Tim Pemusnahan (PJ Rekam Medis)</label>
                    <input type="text" name="nama_ketua" placeholder="Nama Lengkap" class="w-full border p-2.5 rounded-lg text-sm mb-2" required>
                    <input type="text" name="nip_ketua" placeholder="NIP Ketua Tim" class="w-full border p-2.5 rounded-lg text-sm">
                </div>

                <div class="space-y-3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Sekretaris</label>
                    <input type="text" name="nama_saksi1" placeholder="Nama Sekretaris" class="w-full border p-2.5 rounded-lg text-sm mb-2" required>
                    <input type="text" name="nip_saksi1" placeholder="NIP Sekretaris" class="w-full border p-2.5 rounded-lg text-sm">
                </div>

                <div class="space-y-3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Kasubag TU</label>
                    <input type="text" name="nama_saksi2" placeholder="Nama Kasubag TU" class="w-full border p-2.5 rounded-lg text-sm mb-2">
                    <input type="text" name="nip_saksi2" placeholder="NIP Kasubag TU" class="w-full border p-2.5 rounded-lg text-sm">
                </div>

                <div class="space-y-3 bg-red-50 p-5 rounded-xl border border-red-100 shadow-inner">
                    <label class="block text-[10px] font-bold text-red-700 uppercase tracking-tighter">Mengesahkan (Kepala Puskesmas)</label>
                    <input type="text" name="nama_kapus" placeholder="Nama Kepala Puskesmas" class="w-full border p-2.5 rounded-lg text-sm mb-2 bg-white" required>
                    <input type="text" name="nip_kapus" placeholder="NIP Kapus" class="w-full border p-2.5 rounded-lg text-sm bg-white">
                </div>
            </div>
            
            <button type="submit" class="w-full py-4 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-200 active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                CETAK BERITA ACARA PEMUSNAHAN REKAM MEDIS
            </button>
        </form>
    </div>
</div>
@endsection