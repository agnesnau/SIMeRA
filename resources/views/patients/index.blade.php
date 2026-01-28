@extends('layouts.app')
@section('title', 'Master Data Pasien')

@section('content')
{{-- PEMBUNGKUS UTAMA HARUS DI ATAS --}}
<div class="space-y-6">
    <!-- NOTIFIKASI ERROR/SUKSES -->
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800">Terjadi Kesalahan!</h3>
                <ul class="list-disc list-inside text-sm text-red-700 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- 1. HEADER & PENCARIAN -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('patients.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 pl
                
                -3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No RM..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 w-full text-sm outline-none transition-all">
            </div>

            <select name="status" onchange="this.form.submit()" class="py-2 pl-3 pr-8 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white cursor-pointer outline-none">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif (Pelayanan)</option>
                <option value="Inaktif" {{ request('status') == 'Inaktif' ? 'selected' : '' }}>Inaktif (> 2 Tahun)</option>
                <option value="Siap Musnah" {{ request('status') == 'Siap Musnah' ? 'selected' : '' }}>Siap Musnah (> 4 Tahun)</option>
                <option value="digudang" {{ request('status') == 'digudang' ? 'selected' : '' }}>📁 Arsip Di Gudang</option>
                <option value="dimusnahkan" {{ request('status') == 'dimusnahkan' ? 'selected' : '' }}>⚠️ Dimusnahkan</option>
            </select>
        </form>

        @if(strtolower(auth()->user()->level) !== 'supervisor')
        <div class="flex gap-2">
            <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg text-sm hover:bg-blue-100 transition flex items-center gap-2 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Excel
            </button>
            <a href="{{ route('patients.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pasien
            </a>
        </div>
        @endif
    </div>

    <!-- 2. AREA AKSI MASSAL -->
    <div id="bulk-action-area" class="hidden">
        <form action="{{ route('patients.bulkAction') }}" method="POST" id="form-massal" class="bg-gray-800 p-4 rounded-xl flex flex-wrap gap-4 items-center shadow-lg text-white border border-gray-700">
            @csrf
            <input type="hidden" name="ids" id="input-ids-terpilih">
            <div class="text-sm font-bold flex items-center gap-2">
                <span class="bg-gray-700 px-3 py-1 rounded-full text-xs text-white border border-gray-600" id="jumlah-terpilih">0</span> 
                <span>Data Terpilih</span>
            </div>
            <div class="h-6 w-px bg-gray-600 mx-2"></div>
            <select name="action_type" class="p-2 rounded-lg text-sm bg-gray-700 text-white border border-gray-600 outline-none font-medium" required>
                <option value="">-- Pilih Tindakan --</option>
                <option value="hapus">🗑️ Hapus Data Permanen</option>
            </select>
            <button type="submit" onclick="return confirm('Proses data terpilih?')" class="bg-white text-gray-900 px-6 py-2 rounded-lg font-black text-sm hover:bg-emerald-50 transition ml-auto">PROSES</button>
        </form>
    </div>

    <!-- 3. TABEL DATA -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-bold border-b-2 border-gray-200">
                    <tr>
                        @if(strtolower(auth()->user()->level) !== 'supervisor')
                        <th class="px-6 py-4 text-center w-12 bg-gray-50">
                            <input type="checkbox" id="pilih-semua" class="w-4 h-4 text-emerald-600 rounded border-gray-300 cursor-pointer">
                        </th>
                        @endif
                        <th class="px-6 py-4">No RM</th>
                        <th class="px-6 py-4">Identitas Pasien</th>
                        <th class="px-6 py-4">Kunjungan Terakhir</th>
                        <th class="px-6 py-4 text-center">Status Retensi</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($patients as $p)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        @if(strtolower(auth()->user()->level) !== 'supervisor')
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" class="check-pasien w-4 h-4 text-emerald-600 rounded border-gray-300 cursor-pointer" value="{{ $p->id }}">
                        </td>
                        @endif

                        <td class="px-6 py-4 font-bold text-emerald-700 font-mono">{{ $p->no_rm }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 uppercase">{{ $p->nama_pasien }}</div>
                            <div class="text-[10px] text-gray-400 font-mono mt-0.5">NIK: {{ $p->nik ?? '-' }}</div>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            @if($p->lastVisit)
                                <div class="font-medium text-gray-800">{{ $p->lastVisit->tgl_kunjungan->format('d M Y') }}</div>
                                <div class="text-[10px] text-gray-400 italic">{{ $p->lastVisit->tgl_kunjungan->diffForHumans() }}</div>
                            @else
                                <span class="text-gray-400 italic text-xs bg-gray-50 px-2 py-1 rounded">Belum Ada Data</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
    {{-- LOGIKA PRIORITAS: Cek Status Manual Dulu --}}
    
    @if($p->manual_status === 'digudang')
        {{-- 1. JIKA SUDAH DI GUDANG --}}
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800 border border-gray-200 uppercase tracking-wide">
            📁 Di Gudang
        </span>

    @elseif($p->manual_status === 'pemilahan')
        {{-- 2. JIKA SEDANG DIPILAH --}}
        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wide animate-pulse">
            ⚠️ Sedang Dipilah
        </span>

    @else
        {{-- 3. JIKA TIDAK ADA STATUS MANUAL, BARU HITUNG TANGGAL (OTOMATIS) --}}
        @php 
            $status = $p->current_status; // Mengambil dari Accessor Model
        @endphp
        
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wide
            {{ $status == 'Aktif' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 
              ($status == 'Inaktif' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 
              'bg-red-100 text-red-800 border-red-200') }}">
            {{ $status }}
        </span>
    @endif
</td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- TOMBOL RESUME MEDIS (LINK HALAMAN PENUH - SANGAT KLINIS) -->
                                <a href="{{ route('patients.show', $p->id) }}" 
                                   class="flex items-center gap-2 px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm active:scale-95" 
                                   title="Buka Resume Medis Selayar Penuh">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    <span class="text-[11px] font-black uppercase">Detail</span>
                                </a>

                                @if(strtolower(auth()->user()->level) !== 'supervisor')
                                    <a href="{{ route('patients.edit', $p->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg border border-blue-200 hover:bg-blue-100 transition shadow-sm" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('patients.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus data pasien?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg border border-red-200 hover:bg-red-100 transition shadow-sm" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 bg-gray-50/50 italic">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50">{{ $patients->withQueryString()->links() }}</div>
    </div>
</div>

<!-- 4. MODAL IMPORT EXCEL -->
@if(strtolower(auth()->user()->level) !== 'supervisor')
<div id="importModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-blue-600 p-4 text-white flex justify-between items-center">
            <h3 class="font-black uppercase text-sm tracking-widest">Migrasi Data Excel</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-white hover:text-blue-200 text-xl">&times;</button>
        </div>
        <form action="{{ route('patients.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="mb-6">
                <label class="block text-xs font-black text-gray-500 uppercase mb-3">Pilih File (.xlsx / .csv)</label>
                <input type="file" name="file" required class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer outline-none">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold">BATAL</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-xs font-black shadow-md">MULAI IMPORT</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pilihSemua = document.getElementById('pilih-semua');
        const checkboxes = document.querySelectorAll('.check-pasien');
        const bulkArea = document.getElementById('bulk-action-area');
        const labelJumlah = document.getElementById('jumlah-terpilih');
        const inputIds = document.getElementById('input-ids-terpilih');

        if(pilihSemua) {
            pilihSemua.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkStatus();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkStatus);
        });

        function updateBulkStatus() {
            const terpilih = Array.from(document.querySelectorAll('.check-pasien:checked')).map(cb => cb.value);
            if(inputIds) inputIds.value = terpilih.join(',');
            if(labelJumlah) labelJumlah.innerText = terpilih.length;
            if (terpilih.length > 0 && bulkArea) {
                bulkArea.classList.remove('hidden');
            } else if (bulkArea) {
                bulkArea.classList.add('hidden');
            }
        }
    });
</script>
@endsection