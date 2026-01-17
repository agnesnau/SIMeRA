@extends('layouts.app')
@section('title', 'Pelaporan & Berita Acara')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Info Card -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-red-50 p-4 rounded-xl border border-red-100 flex items-center gap-3">
            <div class="p-3 bg-white rounded-full text-red-500 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Berkas Siap Musnah</p>
                <h3 class="text-xl font-bold text-red-700">{{ $siapMusnah }}</h3>
            </div>
        </div>
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 flex items-center gap-3">
            <div class="p-3 bg-white rounded-full text-gray-500 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Riwayat Dimusnahkan</p>
                <h3 class="text-xl font-bold text-gray-700">{{ $sudahMusnah }}</h3>
            </div>
        </div>
    </div>

    <!-- Form Cetak -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-emerald-50">
            <h3 class="font-bold text-emerald-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Buat Berita Acara Pemusnahan
            </h3>
        </div>
        
        <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank" class="p-6 space-y-4">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                    <input type="text" name="no_surat" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" value="001/BA-PM/RS/{{ date('Y') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pelaksanaan</label>
                    <input type="date" name="tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Pemusnahan</label>
                <input type="text" name="lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" value="Halaman Belakang Gedung Arsip">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ketua Pelaksana</label>
                    <input type="text" name="ketua" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Nama Lengkap">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP Ketua</label>
                    <input type="text" name="nip_ketua" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="NIP">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Generate & Cetak PDF
                </button>
            </div>
        </form>
    </div>
</div>
@endsection