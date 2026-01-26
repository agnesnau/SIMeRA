@extends('layouts.app')
@section('title', 'Resume Rekam Medis Pasien')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-10">
    
    <!-- 1. HEADER RINGKASAN KLINIS -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-emerald-700 px-8 py-6 text-white flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-6 w-full">
                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 shadow-lg">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight leading-none mb-2">{{ $patient->nama_pasien }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm font-medium text-emerald-100">
                        <span class="bg-white/20 px-3 py-1 rounded-full border border-white/10 font-mono">RM: {{ $patient->no_rm }}</span>
                        <span class="opacity-50">|</span>
                        <span>NIK: {{ $patient->nik ?? 'N/A' }}</span>
                        <span class="opacity-50">|</span>
                        <span>BPJS: {{ $patient->no_bpjs ?? '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <a href="{{ route('patients.edit', $patient->id) }}" class="flex-1 md:flex-none px-5 py-2.5 bg-white text-emerald-800 rounded-xl text-sm font-bold hover:bg-emerald-50 transition shadow-md">
                    Sunting Data
                </a>
                <a href="{{ route('patients.index') }}" class="flex-1 md:flex-none px-5 py-2.5 bg-emerald-800 text-white rounded-xl text-sm font-bold hover:bg-emerald-900 transition border border-emerald-600">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Dashboard Indikator Cepat -->
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-slate-100 border-t border-slate-100 bg-slate-50/50">
            <div class="p-5 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Usia Pasien</p>
                <p class="text-xl font-black text-slate-700">{{ \Carbon\Carbon::parse($patient->tgl_lahir)->age }} <span class="text-xs font-normal">Tahun</span></p>
            </div>
            <div class="p-5 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Jenis Kelamin</p>
                <p class="text-xl font-black text-slate-700">{{ $patient->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>
            <div class="p-5 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Kunjungan Terakhir</p>
                <p class="text-xl font-black text-slate-700">{{ $patient->lastVisit ? $patient->lastVisit->tgl_kunjungan->format('d/m/Y') : '-' }}</p>
            </div>
            <div class="p-5 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Status Retensi</p>
                @php $status = $patient->current_status; @endphp
                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter 
                    {{ $status == 'Aktif' ? 'bg-green-100 text-green-700' : ($status == 'Inaktif' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                    {{ $status }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KOLOM KIRI: DETAIL DEMOGRAFI -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2 bg-slate-50/50">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h3 class="font-bold text-slate-700 text-sm uppercase">Informasi Identitas & Domisili</h3>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tempat, Tanggal Lahir</p>
                        <p class="text-slate-700 font-semibold">{{ $patient->tempat_lahir ?? '-' }}, {{ \Carbon\Carbon::parse($patient->tgl_lahir)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kontak HP / Telepon</p>
                        <p class="text-slate-700 font-mono font-bold">{{ $patient->no_hp ?? '-' }}</p>
                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Alamat Lengkap (Sesuai KTP)</p>
                        <div class="mt-2 p-4 bg-slate-50 rounded-xl border border-slate-200 text-slate-600 leading-relaxed italic">
                            "{{ $patient->alamat_lengkap ?? '-' }}"
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL RIWAYAT KUNJUNGAN (HISTORI MEDIS) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-700 text-sm uppercase flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Histori Pelayanan Medis
                    </h3>
                    <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-lg font-black">{{ $patient->visits->count() }} Kunjungan</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b">
                            <tr>
                                <th class="px-6 py-4">Tanggal Kunjungan</th>
                                <th class="px-6 py-4">Poli / Unit</th>
                                <th class="px-6 py-4">Dokter Pemeriksa</th>
                                <th class="px-6 py-4">Diagnosa Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($patient->visits->sortByDesc('tgl_kunjungan') as $visit)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-700">{{ $visit->tgl_kunjungan->translatedFormat('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $visit->no_registrasi }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-md border border-blue-100 text-[10px] font-bold uppercase">{{ $visit->poli_tujuan }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">{{ $visit->dokter ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-500 italic">{{ \Illuminate\Support\Str::limit($visit->diagnosa, 50) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">Belum ada catatan histori pelayanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: TRACKER SIKLUS HIDUP ARSIP -->
        <div class="space-y-6">
            
            <!-- Life-Cycle Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6 border-t-4 border-t-emerald-600">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Siklus Hidup Berkas RM
                </h3>
                
                <div class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                    <!-- Step 1: Aktif -->
                    <div class="relative flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center shadow-lg shadow-green-100 border-4 border-white z-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-slate-700 uppercase">Masa Aktif</h4>
                                <p class="text-[10px] text-slate-400">Penyimpanan Rak Utama</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-green-600">Selesai</span>
                    </div>

                    <!-- Step 2: Inaktif -->
                    @php $isInactive = $years >= 2; @endphp
                    <div class="relative flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full {{ $isInactive ? 'bg-amber-500' : 'bg-slate-200' }} text-white flex items-center justify-center border-4 border-white z-10 shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black {{ $isInactive ? 'text-slate-700' : 'text-slate-400' }} uppercase">Masa Inaktif</h4>
                                <p class="text-[10px] text-slate-400">Gudang Penyusutan</p>
                            </div>
                        </div>
                        @if($isInactive) <span class="text-[10px] font-bold text-amber-600">Sedang Berjalan</span> @endif
                    </div>

                    <!-- Step 3: Penilaian/Musnah -->
                    @php $isReady = $years >= 4; @endphp
                    <div class="relative flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full {{ $isReady ? 'bg-red-500' : 'bg-slate-100 text-slate-300' }} text-white flex items-center justify-center border-4 border-white z-10 shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black {{ $isReady ? 'text-slate-700' : 'text-slate-400' }} uppercase">Penilaian Akhir</h4>
                                <p class="text-[10px] text-slate-400">Pemusnahan Berkas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Digital Asset Card -->
            <div class="bg-blue-600 rounded-2xl shadow-xl overflow-hidden p-6 text-white relative">
                <div class="absolute -right-4 -top-4 opacity-10">
                    <svg width="120" height="120" fill="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xs font-black text-blue-200 uppercase tracking-widest mb-4">Arsip Digital Abadi</h3>
                @php
                    $digitalFile = \App\Models\RetentionAction::where('patient_id', $patient->id)->whereNotNull('file_path')->latest()->first();
                @endphp
                
                @if($digitalFile)
                    <p class="text-[10px] font-medium leading-relaxed mb-4 opacity-80 italic">"Lembar Bernilai Guna Tinggi telah didigitalisasi dan tersimpan aman di server."</p>
                    <a href="{{ asset('storage/' . $digitalFile->file_path) }}" target="_blank" class="block w-full py-3 bg-white text-blue-700 text-center rounded-xl font-bold text-xs hover:bg-blue-50 transition shadow-lg">
                        BUKA BERKAS DIGITAL
                    </a>
                @else
                    <div class="py-4 border-2 border-dashed border-blue-400 rounded-xl text-center">
                        <p class="text-[10px] font-bold uppercase tracking-tighter opacity-60 italic">Belum Ada Digitalisasi</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection