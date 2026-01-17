@extends('layouts.app')
@section('title', 'Manajemen Retensi Arsip')

@section('content')
<div class="space-y-6">

    <!-- 1. INFO STATISTIK -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Inaktif -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-l-4 border-yellow-400 flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Total Inaktif (2-4 Th)</p>
                <h3 class="text-2xl font-bold text-yellow-600">{{ $stats['total_inaktif'] ?? 0 }}</h3>
            </div>
            <div class="p-2 bg-yellow-50 rounded-lg text-yellow-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <!-- Siap Musnah -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-l-4 border-red-500 flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Siap Retensi (> 4 Th)</p>
                <h3 class="text-2xl font-bold text-red-600">{{ $stats['siap_musnah'] ?? 0 }}</h3>
            </div>
            <div class="p-2 bg-red-50 rounded-lg text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
        </div>
        <!-- Info Aturan -->
        <div class="bg-blue-50 p-4 rounded-xl border border-blue-200 text-sm text-blue-800 flex flex-col justify-center">
            <p class="font-bold mb-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Aturan Retensi:</p>
            <ul class="list-disc pl-5 space-y-1 text-xs">
                <li><strong>Inaktif:</strong> Tidak berkunjung 2 s.d 4 tahun.</li>
                <li><strong>Siap Musnah:</strong> Tidak berkunjung > 4 tahun.</li>
            </ul>
        </div>
    </div>

    <!-- 2. TABEL DATA -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Toolbar Filter -->
        <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50">
            <h3 class="font-bold text-gray-700 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Daftar Retensi Arsip
            </h3>
            
            <form action="{{ route('retensi.index') }}" method="GET" class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600">Filter Status:</label>
                <select name="status" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 py-2 px-3 cursor-pointer">
                    <option value="">Semua Data</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif (< 2 Th)</option>
                    <option value="Inaktif" {{ request('status') == 'Inaktif' ? 'selected' : '' }}>Inaktif (2-4 Th)</option>
                    <option value="Siap Musnah" {{ request('status') == 'Siap Musnah' ? 'selected' : '' }}>Siap Musnah (> 4 Th)</option>
                </select>
            </form>
        </div>

        <!-- Tabel Konten -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-gray-600 font-bold border-b">
                    <tr>
                        <th class="px-6 py-4">No RM</th>
                        <th class="px-6 py-4">Pasien</th>
                        <th class="px-6 py-4">Kunjungan Terakhir</th>
                        <th class="px-6 py-4">Masa Retensi</th>
                        <th class="px-6 py-4 text-center">Status Sistem</th>
                        <th class="px-6 py-4 text-center">Aksi / Proses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paginatedPatients as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-emerald-600 font-mono">{{ $p->no_rm }}</td>
                        <td class="px-6 py-4 font-medium">{{ $p->nama_pasien }}</td>
                        
                        <td class="px-6 py-4">
                            @if($p->lastVisit)
                                <div class="text-gray-800">{{ $p->lastVisit->tgl_kunjungan->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $p->lastVisit->tgl_kunjungan->diffForHumans() }}</div>
                            @else
                                <span class="text-gray-400 italic text-xs">Belum ada data</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            @if($p->lastVisit)
                                {{ $p->lastVisit->tgl_kunjungan->diffInYears(now()) }} Tahun
                            @else
                                -
                            @endif
                        </td>

                        <!-- STATUS LABEL -->
                        <td class="px-6 py-4 text-center">
                            @if($p->current_status == 'Aktif')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                            @elseif($p->current_status == 'Inaktif')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">Inaktif</span>
                            @elseif($p->current_status == 'Siap Musnah')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 animate-pulse">Siap Musnah</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Dimusnahkan</span>
                            @endif
                        </td>

                        <!-- TOMBOL AKSI -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                
                                <!-- 1. Tombol Upload Nilai Guna -->
                                @if(in_array($p->current_status, ['Inaktif', 'Siap Musnah']))
                                    <button type="button" 
                                        data-url="{{ route('retensi.verify', $p->id) }}"
                                        data-name="{{ $p->nama_pasien }}"
                                        onclick="openUploadModal(this)"
                                        class="flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 rounded text-xs font-medium hover:bg-blue-100 transition border border-blue-200" 
                                        title="Upload Berkas Nilai Guna">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        Upload Nilai Guna
                                    </button>
                                @endif

                                <!-- 2. Tombol RETENSI -->
                                @if(in_array($p->current_status, ['Inaktif', 'Siap Musnah']))
                                    <form action="{{ route('retensi.move', $p->id) }}" method="POST" onsubmit="return confirm('Pindahkan berkas RM {{ $p->no_rm }} ke daftar SIAP MUSNAH?');">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-600 rounded text-xs font-medium hover:bg-red-100 transition border border-red-200 shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                            Retensi
                                        </button>
                                    </form>
                                @elseif($p->current_status == 'Aktif')
                                    <span class="text-xs text-green-600 italic">Masa Aktif</span>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 bg-gray-50/50">
                            Tidak ada data retensi yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
            {{ $paginatedPatients->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- 3. MODAL UPLOAD NILAI GUNA (Wajib Ada) -->
<div id="uploadModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto p-6 animate-fade-in-down relative">
        
        <!-- Header Modal -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Upload Nilai Guna</h3>
            <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- Form Upload -->
        <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100 text-sm text-blue-800">
                Pasien: <span id="modalPatientName" class="font-bold"></span>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto / Scan Berkas Fisik</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition bg-gray-50 cursor-pointer relative group">
                    <input type="file" name="file_nilai_guna" accept="image/*,application/pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="text-gray-500 group-hover:text-blue-500 transition-colors">
                        <svg class="mx-auto h-10 w-10 text-gray-400 mb-2 group-hover:text-blue-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <p class="text-sm">Klik untuk pilih gambar/PDF</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG, PDF. Maks 2MB.</p>
            </div>
            
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 shadow-sm transition-colors">Upload & Verifikasi</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. SCRIPT PENGHUBUNG TOMBOL & MODAL -->
<script>
    function openUploadModal(element) {
        // Ambil data dari atribut tombol
        let url = element.getAttribute('data-url');
        let name = element.getAttribute('data-name');
        
        if (!url) {
            console.error('URL upload tidak ditemukan pada tombol');
            return;
        }

        // Isi form action dan nama pasien
        document.getElementById('uploadForm').action = url;
        document.getElementById('modalPatientName').innerText = name;
        
        // Tampilkan Modal
        document.getElementById('uploadModal').classList.remove('hidden');
    }
</script>
@endsection