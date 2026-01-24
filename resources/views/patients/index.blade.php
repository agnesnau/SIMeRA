@extends('layouts.app')
@section('title', 'Master Data Pasien')

@section('content')
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
        <!-- Form Filter -->
        <form action="{{ route('patients.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No RM..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 w-full text-sm outline-none">
            </div>

            <select name="status" onchange="this.form.submit()" class="py-2 pl-3 pr-8 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white cursor-pointer outline-none">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif (Pelayanan)</option>
                <option value="Inaktif" {{ request('status') == 'Inaktif' ? 'selected' : '' }}>Inaktif (> 2 Tahun)</option>
                <option value="Siap Musnah" {{ request('status') == 'Siap Musnah' ? 'selected' : '' }}>Siap Musnah (> 4 Tahun)</option>
            </select>
        </form>

        <!-- Tombol Aksi (Hanya Non-Kepala) -->
        @if(strtolower(auth()->user()->level) !== 'kepala')
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

    <!-- 2. AREA AKSI MASSAL (Muncul Otomatis saat Checkbox dicentang) -->
    <div id="bulk-action-area" class="hidden animate-fade-in-down">
        <form action="{{ route('patients.bulkAction') }}" method="POST" id="form-massal" class="bg-gray-800 p-4 rounded-xl flex flex-wrap gap-4 items-center shadow-lg text-white border border-gray-700">
            @csrf
            <input type="hidden" name="ids" id="input-ids-terpilih">
            
            <div class="text-sm font-bold flex items-center gap-2">
                <span class="bg-gray-700 px-3 py-1 rounded-full text-xs text-white border border-gray-600" id="jumlah-terpilih">0</span> 
                <span>Data Terpilih</span>
            </div>

            <div class="h-6 w-px bg-gray-600 mx-2"></div>

            <select name="action_type" class="p-2 rounded-lg text-sm bg-gray-700 text-white border border-gray-600 outline-none font-medium cursor-pointer focus:ring-2 focus:ring-emerald-500" required>
                <option value="">-- Pilih Tindakan --</option>
                <option value="hapus">🗑️ Hapus Data Permanen</option>
                <!-- Opsi lain dihapus agar tidak redundant dengan halaman Retensi -->
            </select>

            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memproses data terpilih? Tindakan ini tidak dapat dibatalkan.')" class="bg-white text-gray-900 px-6 py-2 rounded-lg font-black text-sm hover:bg-emerald-50 transition shadow-sm uppercase tracking-wider ml-auto flex items-center gap-2">
                <span>Proses</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
        </form>
    </div>

    <!-- 3. TABEL DATA -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-bold border-b-2 border-gray-200">
                    <tr>
                        <!-- Checkbox hanya untuk Admin/Petugas -->
                        @if(strtolower(auth()->user()->level) !== 'kepala')
                        <th class="px-6 py-4 text-center w-12 bg-gray-50">
                            <input type="checkbox" id="pilih-semua" class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer transition">
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
                        <!-- Checkbox Baris -->
                        @if(strtolower(auth()->user()->level) !== 'kepala')
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" class="check-pasien w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer transition" value="{{ $p->id }}">
                        </td>
                        @endif

                        <td class="px-6 py-4 font-bold text-emerald-700 font-mono tracking-tighter">{{ $p->no_rm }}</td>
                        
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 uppercase">{{ $p->nama_pasien }}</div>
                            <div class="text-[10px] text-gray-400 font-mono flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                {{ $p->nik ?? '-' }}
                            </div>
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
                            @php $status = $p->current_status; @endphp
                            
                            @if($status == 'Aktif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800 border border-green-200 uppercase tracking-wide">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> AKTIF
                                </span>
                            @elseif($status == 'Inaktif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800 border border-yellow-200 uppercase tracking-wide">
                                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span> INAKTIF
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200 uppercase tracking-wide">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span> SIAP MUSNAH
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- 1. Tombol Detail (Memicu Modal dengan Data Atribut Aman) -->
                                <button type="button" 
                                        onclick="openDetailModal(this)"
                                        data-json="{{ json_encode($p->only(['nama_pasien', 'no_rm', 'nik', 'current_status'])) }}"
                                        data-visit="{{ $p->lastVisit ? $p->lastVisit->tgl_kunjungan->format('d F Y') : '-' }}"
                                        class="p-2 bg-gray-50 text-gray-500 rounded-lg hover:bg-emerald-50 hover:text-emerald-600 transition shadow-sm border border-gray-200" 
                                        title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>

                                <!-- 2. Tombol Edit & Hapus (Hanya untuk NON-Kepala) -->
                                @if(strtolower(auth()->user()->level) !== 'kepala')
                                    <a href="{{ route('patients.edit', $p->id) }}" 
                                       class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition shadow-sm border border-blue-200" 
                                       title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>

                                    <form action="{{ route('patients.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pasien {{ $p->nama_pasien }}? \n\nPERINGATAN: Data riwayat kunjungan juga akan terhapus!');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition shadow-sm border border-red-200" 
                                                title="Hapus Permanen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 bg-gray-50/50 italic border-b border-gray-100">
                            Tidak ada data pasien yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            {{ $patients->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- 4. MODAL IMPORT EXCEL (Disembunyikan jika Kepala) -->
@if(strtolower(auth()->user()->level) !== 'kepala')
<div id="importModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="bg-blue-600 p-4 text-white flex justify-between items-center">
            <h3 class="font-black uppercase text-sm tracking-widest">Migrasi Data Excel</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-white hover:text-blue-200 text-xl">&times;</button>
        </div>
        <form action="{{ route('patients.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="mb-6 text-left">
                <label class="block text-xs font-black text-gray-500 uppercase mb-3">Pilih File (.xlsx / .csv)</label>
                <input type="file" name="file" required class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer outline-none">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-200">BATAL</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-xs font-black shadow-md hover:bg-blue-700">MULAI IMPORT</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- 5. MODAL DETAIL PASIEN (DESAIN CANTIK) -->
<div id="detailModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-4" onclick="closeDetailModal(event)">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all animate-fade-in-up border border-gray-100 relative">
        <!-- Header Gradien -->
        <div class="h-24 bg-gradient-to-br from-emerald-600 to-emerald-900 relative">
            <button onclick="document.getElementById('detailModal').classList.add('hidden')" class="absolute top-4 right-4 bg-black/20 text-white hover:bg-black/40 rounded-full p-1.5 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="absolute -bottom-10 left-8">
                <div class="w-20 h-20 bg-white rounded-2xl p-1.5 shadow-lg">
                    <div class="w-full h-full bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- Isi Modal -->
        <div class="pt-12 px-8 pb-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-800 uppercase" id="dm-nama">Nama Pasien</h2>
                    <p class="text-sm text-gray-500 font-medium font-mono mt-1" id="dm-norm">RM-000000</p>
                </div>
                <div id="dm-status-badge"></div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">NIK / Identitas</p>
                    <p class="text-sm font-bold text-gray-700 mt-1 font-mono" id="dm-nik">-</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Kunjungan Terakhir</p>
                    <p class="text-sm font-bold text-gray-700 mt-1" id="dm-visit">-</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                <button onclick="document.getElementById('detailModal').classList.add('hidden')" class="px-6 py-2 bg-gray-100 text-gray-600 font-bold rounded-lg text-xs hover:bg-gray-200 transition">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pilihSemua = document.getElementById('pilih-semua');
        const checkboxes = document.querySelectorAll('.check-pasien');
        const bulkArea = document.getElementById('bulk-action-area');
        const labelJumlah = document.getElementById('jumlah-terpilih');
        const inputIds = document.getElementById('input-ids-terpilih');

        // Logic Pilih Semua
        if(pilihSemua) {
            pilihSemua.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkStatus();
            });
        }

        // Logic Klik Satu-satu
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkStatus);
        });

        function updateBulkStatus() {
            const terpilih = Array.from(document.querySelectorAll('.check-pasien:checked')).map(cb => cb.value);
            
            if(inputIds) inputIds.value = terpilih.join(',');
            if(labelJumlah) labelJumlah.innerText = terpilih.length;
            
            // Tampilkan area aksi massal jika ada yang dipilih
            if (terpilih.length > 0 && bulkArea) {
                bulkArea.classList.remove('hidden');
            } else if (bulkArea) {
                bulkArea.classList.add('hidden');
            }
        }
    });

    // Fungsi Pop-up Detail (Menggunakan data attribute agar aman)
    function openDetailModal(btn) {
        // Ambil data JSON dari atribut tombol
        const data = JSON.parse(btn.getAttribute('data-json'));
        const lastVisit = btn.getAttribute('data-visit');

        document.getElementById('dm-nama').innerText = data.nama_pasien;
        document.getElementById('dm-norm').innerText = data.no_rm;
        document.getElementById('dm-nik').innerText = data.nik ? data.nik : '-';
        document.getElementById('dm-visit').innerText = lastVisit;

        const badgeContainer = document.getElementById('dm-status-badge');
        let badgeClass = "bg-gray-100 text-gray-600";
        if(data.current_status == 'Aktif') badgeClass = "bg-green-100 text-green-700 border-green-200";
        else if(data.current_status == 'Inaktif') badgeClass = "bg-yellow-100 text-yellow-700 border-yellow-200";
        else if(data.current_status == 'Siap Musnah') badgeClass = "bg-red-100 text-red-700 border-red-200";

        badgeContainer.innerHTML = `<span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border ${badgeClass}">${data.current_status}</span>`;
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal(event) {
        if(event.target.id === 'detailModal') {
            document.getElementById('detailModal').classList.add('hidden');
        }
    }
</script>
@endsection