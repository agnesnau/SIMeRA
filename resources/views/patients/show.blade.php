@extends('layouts.app')
@section('title', 'Detail Data Pasien')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Header & Navigasi -->
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xl">
                {{ substr($patient->nama_pasien, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $patient->nama_pasien }}</h2>
                <p class="text-gray-500 text-sm font-mono">{{ $patient->no_rm }}</p>
            </div>
        </div>
        <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
            &larr; Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kiri: Informasi Biodata -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 border-b pb-3 mb-4">Biodata Lengkap</h3>
            
            <div class="grid grid-cols-2 gap-y-4 text-sm">
                <div>
                    <p class="text-gray-500">NIK</p>
                    <p class="font-medium">{{ $patient->nik ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Nomor BPJS</p>
                    <p class="font-medium text-blue-600">{{ $patient->no_bpjs ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Tempat, Tanggal Lahir</p>
                    <p class="font-medium">{{ $patient->tempat_lahir }}, {{ \Carbon\Carbon::parse($patient->tgl_lahir)->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Usia</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($patient->tgl_lahir)->age }} Tahun</p>
                </div>
                <div>
                    <p class="text-gray-500">Jenis Kelamin</p>
                    <p class="font-medium">{{ $patient->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Nomor HP</p>
                    <p class="font-medium">{{ $patient->no_hp ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-gray-500">Alamat Lengkap</p>
                    <p class="font-medium">{{ $patient->alamat_lengkap ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Kanan: Status Retensi & File -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Status Retensi</h3>
                
                <div class="text-center p-4 rounded-lg 
                    {{ $patient->current_status == 'Aktif' ? 'bg-green-50 text-green-700' : 
                      ($patient->current_status == 'Inaktif' ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') }}">
                    <p class="text-xs uppercase font-bold tracking-wider">Status Saat Ini</p>
                    <p class="text-2xl font-bold mt-1">{{ $patient->current_status }}</p>
                </div>

                <div class="mt-4 text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kunjungan Terakhir:</span>
                        <span class="font-medium">{{ $patient->lastVisit ? $patient->lastVisit->tgl_kunjungan->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Masa Retensi:</span>
                        <span class="font-medium">{{ $patient->lastVisit ? $patient->lastVisit->tgl_kunjungan->diffInYears(now()) . ' Tahun' : '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- File Nilai Guna -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Berkas Nilai Guna</h3>
                @php
                    $file = \App\Models\RetentionAction::where('patient_id', $patient->id)->whereNotNull('file_path')->latest()->first();
                @endphp

                @if($file)
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg">
                        <div class="bg-red-100 p-2 rounded text-red-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">File Digital</p>
                            <p class="text-xs text-gray-500">{{ $file->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="block w-full text-center mt-3 px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition">
                        Buka / Download File
                    </a>
                @else
                    <div class="text-center py-6 text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm">Belum ada file diupload.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Riwayat Kunjungan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Riwayat Kunjungan</h3>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 font-medium">
                <tr>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">No Reg</th>
                    <th class="px-6 py-3">Poli</th>
                    <th class="px-6 py-3">Diagnosa</th>
                    <th class="px-6 py-3">Dokter</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($patient->visits as $visit)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $visit->tgl_kunjungan->format('d/m/Y') }}</td>
                    <td class="px-6 py-3 font-mono text-xs">{{ $visit->no_registrasi }}</td>
                    <td class="px-6 py-3">{{ $visit->poli_tujuan }}</td>
                    <td class="px-6 py-3">{{ $visit->diagnosa }}</td>
                    <td class="px-6 py-3">{{ $visit->dokter ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada riwayat kunjungan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection