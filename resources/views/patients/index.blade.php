@extends('layouts.app')
@section('title', 'Master Data Pasien')

@section('content')
<div class="space-y-6">
    
    <!-- BAGIAN 1: FILTER & SEARCH & IMPORT -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        
        <!-- Kiri: Search & Filter -->
        <form action="{{ route('patients.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <!-- Search Input -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No RM..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 w-full md:w-64 text-sm">
            </div>

            <!-- Filter Status -->
            <select name="status" onchange="this.form.submit()" class="py-2 pl-3 pr-8 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white cursor-pointer">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif (Retensi)</option>
                <option value="Inaktif" {{ request('status') == 'Inaktif' ? 'selected' : '' }}>Inaktif (> 2 Tahun)</option>
                <option value="Siap Musnah" {{ request('status') == 'Siap Musnah' ? 'selected' : '' }}>Siap Musnah (> 4 Tahun)</option>
            </select>
        </form>

        <!-- Kanan: Tombol Aksi -->
        <div class="flex gap-2">
            <!-- Tombol Import (Trigger Modal) -->
            <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg text-sm hover:bg-blue-100 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Excel
            </button>

            <!-- Tombol Tambah Manual -->
            <a href="{{ route('patients.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pasien
            </a>
        </div>
    </div>

    <!-- BAGIAN 2: TABEL DATA -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-bold border-b">
                    <tr>
                        <th class="px-6 py-4">No RM</th>
                        <th class="px-6 py-4">Nama Pasien</th>
                        <th class="px-6 py-4">Kunjungan Terakhir</th>
                        <th class="px-6 py-4 text-center">Status Retensi</th>
                        <th class="px-6 py-4 text-center">Berkas Nilai Guna</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($patients as $p)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4 font-bold text-emerald-600 font-mono">{{ $p->no_rm }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $p->nama_pasien }}
                            <div class="text-xs text-gray-400 mt-0.5">{{ $p->nik ?? 'NIK Kosong' }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            @if($p->lastVisit)
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $p->lastVisit->tgl_kunjungan->format('d M Y') }}
                                </div>
                                <span class="text-xs text-gray-400 ml-6">{{ $p->lastVisit->tgl_kunjungan->diffForHumans() }}</span>
                            @else
                                <span class="text-gray-400 italic text-xs">Belum ada kunjungan</span>
                            @endif
                        </td>
                        
                        <!-- STATUS BADGE -->
                        <td class="px-6 py-4 text-center">
                            @php $status = $p->current_status; @endphp
                            @if($status == 'Aktif')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                    Aktif
                                </span>
                            @elseif($status == 'Inaktif')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                    Inaktif
                                </span>
                            @elseif($status == 'Siap Musnah')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                    Siap Musnah
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                    {{ $status }}
                                </span>
                            @endif
                        </td>

                        <!-- KOLOM BARU: BERKAS NILAI GUNA -->
                        <td class="px-6 py-4 text-center">
                            @php
                                // Mencari file upload terakhir dari tabel retention_actions secara langsung
                                $file = \App\Models\RetentionAction::where('patient_id', $p->id)
                                        ->whereNotNull('file_path')
                                        ->latest()
                                        ->first();
                            @endphp

                            @if($file)
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-200 text-xs font-medium hover:bg-emerald-100 transition" title="Lihat Berkas">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat File
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">- Tidak Ada -</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center flex justify-center gap-2">
                            <!-- TOMBOL VIEW (BARU) -->
                            <a href="{{ route('patients.show', $p->id) }}" class="p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>

                            <a href="{{ route('patients.edit', $p->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                            <form action="{{ route('patients.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus data pasien ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 bg-gray-50/50">
                            Tidak ada data yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
            {{ $patients->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<div id="importModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6 animate-fade-in-down">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Import Data Pasien</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form action="{{ route('patients.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Excel (.xlsx/.csv)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition bg-gray-50 cursor-pointer relative">
                    <input type="file" name="file" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <p class="text-sm">Klik untuk pilih file atau drag & drop</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Pastikan format kolom sesuai template.</p>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 shadow-sm">Upload & Proses</button>
            </div>
        </form>
    </div>
</div>
@endsection