@extends('layouts.app')
@section('title', 'Detail Kunjungan')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Detail Kunjungan</h2>
            <p class="text-sm text-gray-500">No. Registrasi: <span class="font-mono font-bold text-emerald-600">{{ $visit->no_registrasi }}</span></p>
        </div>
        <a href="{{ route('visits.index') }}" class="text-sm text-gray-500 hover:text-gray-800 border px-3 py-1 rounded">Kembali</a>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="col-span-2 bg-blue-50 p-4 rounded-lg border border-blue-100">
            <p class="text-xs text-blue-500 font-bold uppercase">Data Pasien</p>
            <p class="text-lg font-bold text-blue-900">{{ $visit->patient->nama_pasien }}</p>
            <p class="text-sm text-blue-700">RM: {{ $visit->patient->no_rm }}</p>
        </div>

        <div>
            <label class="block text-xs text-gray-500 uppercase font-bold">Tanggal Kunjungan</label>
            <p class="text-gray-800 font-medium">{{ $visit->tgl_kunjungan->format('d F Y') }}</p>
        </div>
        <div>
            <label class="block text-xs text-gray-500 uppercase font-bold">Poli Tujuan</label>
            <p class="text-gray-800 font-medium">{{ $visit->poli_tujuan }}</p>
        </div>
        <div>
            <label class="block text-xs text-gray-500 uppercase font-bold">Dokter Pemeriksa</label>
            <p class="text-gray-800 font-medium">{{ $visit->dokter ?? '-' }}</p>
        </div>
        <div>
            <label class="block text-xs text-gray-500 uppercase font-bold">Petugas Input</label>
            <p class="text-gray-800 font-medium">{{ $visit->user->nama_lengkap ?? 'System' }}</p>
        </div>
        <div class="col-span-2">
            <label class="block text-xs text-gray-500 uppercase font-bold">Diagnosa</label>
            <div class="bg-gray-50 p-3 rounded border border-gray-200 mt-1">
                {{ $visit->diagnosa }}
            </div>
        </div>
    </div>
</div>
@endsection