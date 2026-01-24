@extends('layouts.app')
@section('title', 'Catat Kunjungan Baru')

@section('content')
<!-- Menambahkan CSS Select2 untuk fitur pencarian yang bisa diketik -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-radius: 0.5rem;
        border-color: #d1d5db;
        height: 42px;
        padding-top: 5px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
</style>

<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
    
    <div class="mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Form Kunjungan</h2>
        <a href="{{ route('visits.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('visits.store') }}" method="POST">
        @csrf
        
        <!-- Pilih Pasien (Searchable) -->
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-700">Pilih Pasien (Ketik No. RM / Nama)</label>
            <select name="patient_id" id="patient_select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                <option value="" disabled selected>-- Cari Pasien --</option>
                @foreach($patients as $p)
                    <option value="{{ $p->id }}">{{ $p->no_rm }} - {{ $p->nama_pasien }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Gunakan kotak di atas untuk mencari pasien berdasarkan No. RM atau Nama.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <!-- Tanggal -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal Kunjungan</label>
                <input type="date" name="tgl_kunjungan" value="{{ date('Y-m-d') }}" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            
            <!-- Poli -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Poli Tujuan</label>
                <select name="poli_tujuan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="Poli Umum">Poli Umum</option>
                    <option value="Poli Gigi">Poli Gigi</option>
                    <option value="Poli KIA">Poli KIA</option>
                </select>
            </div>
        </div>

        <!-- Dokter (Dropdown dengan 4 Pilihan) -->
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-700">Dokter Penanggung Jawab</label>
            <select name="dokter" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                <option value="" disabled selected>-- Pilih Dokter --</option>
                <option value="dr. Suharyo, Sp.PD">dr. Suharyo, Sp.PD</option>
                <option value="dr. Mulyadi, Sp.B">dr. Mulyadi, Sp.B</option>
                <option value="dr. Indah Permatasari">dr. Indah Permatasari</option>
                <option value="dr. Pratiwi Kartika">dr. Pratiwi Kartika</option>
            </select>
        </div>

        <!-- Diagnosa -->
        <div class="mb-6">
            <label class="block mb-2 text-sm font-medium text-gray-700">Diagnosa Utama</label>
            <textarea name="diagnosa" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Contoh: Febris, Dyspepsia..."></textarea>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <button type="reset" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">Reset</button>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium shadow-lg">Simpan Kunjungan</button>
        </div>
    </form>
</div>

<!-- Scripts untuk inisialisasi Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#patient_select').select2({
            placeholder: "-- Cari No. RM atau Nama --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection