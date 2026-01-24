@extends('layouts.app')
@section('title', 'Detail Kunjungan Pasien')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Tombol Kembali -->
    <div class="flex justify-start">
        <a href="{{ route('visits.index') }}" class="text-sm text-gray-500 hover:text-emerald-600 flex items-center gap-1 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Riwayat
        </a>
    </div>

    <!-- Card Utama Detail -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-emerald-600 px-8 py-6 text-white flex justify-between items-center">
            <div>
                <p class="text-emerald-100 text-xs font-bold uppercase tracking-widest mb-1">Informasi Registrasi</p>
                <h2 class="text-2xl font-bold font-mono">{{ $visit->no_registrasi }}</h2>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs font-bold uppercase">
                    Status: Selesai
                </span>
            </div>
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Sisi Kiri: Identitas Pasien -->
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-1">Identitas Pasien</label>
                    <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">
                            {{ substr($visit->patient->nama_pasien, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 leading-tight">{{ $visit->patient->nama_pasien }}</p>
                            <p class="text-sm text-emerald-600 font-mono">{{ $visit->patient->no_rm }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-1">Waktu Pelayanan</label>
                    <p class="text-gray-800 font-medium text-lg">{{ $visit->tgl_kunjungan->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>

            <!-- Sisi Kanan: Detail Medis -->
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-1">Poli Tujuan</label>
                        <p class="text-blue-700 font-bold bg-blue-50 px-3 py-1 rounded-lg border border-blue-100 inline-block">
                            {{ $visit->poli_tujuan }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-1">Dokter Pemeriksa</label>
                        <p class="text-gray-800 font-medium">{{ $visit->dokter }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-1">Diagnosa Utama / Keterangan</label>
                    <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 text-gray-700 leading-relaxed italic">
                        "{{ $visit->diagnosa ?? 'Tidak ada catatan diagnosa spesifik.' }}"
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Card -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-2 text-xs text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Dicatat oleh: <span class="font-bold">{{ $visit->user->nama_lengkap ?? 'Sistem' }}</span>
            </div>
            <p class="text-[10px] text-gray-300">ID Entry: #{{ $visit->id }}</p>
        </div>
    </div>
</div>
@endsection