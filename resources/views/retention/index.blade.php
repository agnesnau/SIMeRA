@extends('layouts.app')
@section('title', 'Manajemen Retensi Berkas RM')

@section('content')
<div class="space-y-6">
    
    <!-- Notifikasi Sukses/Gagal -->
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded shadow-sm animate-fade-in-down">
        <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm animate-fade-in-down">
        <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    <!-- 1. HEADER (DESAIN SIMPEL & BERSIH) -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase italic">Penyusutan Berkas <span class="text-emerald-600">Retensi</span></h2>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                Waktu Sistem: <span class="text-emerald-700">{{ now()->format('d F Y | H:i') }} WIB</span>
            </p>
        </div>

        <!-- Filter & Search Group -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <form action="{{ route('retention.index') }}" method="GET" id="filter-form" class="flex flex-wrap gap-2 w-full md:w-auto">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No RM / Nama..." 
                        class="pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 text-sm outline-none w-full md:w-48 transition-all">
                </div>

                <select name="status_retensi" onchange="this.form.submit()" class="py-2 pl-3 pr-8 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 text-xs font-bold text-gray-600 outline-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="Inaktif" {{ request('status_retensi') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                    <option value="Siap Musnah" {{ request('status_retensi') == 'Siap Musnah' ? 'selected' : '' }}>Siap Musnah</option>
                </select>

                <input type="hidden" name="sort_order" id="sort_order" value="{{ request('sort_order', 'desc') }}">
                <button type="button" onclick="toggleSort()" class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-emerald-50 transition-all text-gray-600 shadow-sm">
                    @if(request('sort_order', 'desc') === 'desc')
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                    @else
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-1l4 4m0 0l4-4m-4 4V4"></path></svg>
                    @endif
                </button>
            </form>
        </div>
    </div>

    <!-- 2. BULK ACTION BAR -->
    <div id="bulk-action-area" class="hidden animate-fade-in-down">
        <!-- Form Bulk harus punya enctype karena ada kemungkinan upload massal di masa depan -->
        <form action="{{ route('retention.bulkAction') }}" method="POST" class="bg-gray-800 p-4 rounded-2xl flex items-center gap-4 shadow-xl text-white border border-gray-700">
            @csrf
            <input type="hidden" name="ids" id="input-ids-terpilih">
            <div class="flex items-center gap-3 border-r border-gray-700 pr-4">
                <div class="bg-emerald-500 text-white w-6 h-6 flex items-center justify-center rounded-lg text-[10px] font-black" id="jumlah-terpilih">0</div>
                <div class="text-[10px] font-black uppercase tracking-widest">Terpilih</div>
            </div>
            
            <select name="action_type" class="p-2 rounded-lg text-xs bg-gray-700 text-white border-none outline-none cursor-pointer font-bold uppercase" required>
                <option value="">-- Pilih Tindakan --</option>
                <option value="pindahkan">🚀 Pindahkan ke Siap Musnah</option>
                <option value="nilai_guna">💎 Tandai Nilai Guna</option>
            </select>

            <button type="submit" onclick="return confirm('Jalankan aksi massal?')" class="bg-emerald-600 text-white px-6 py-2 rounded-xl font-black text-xs hover:bg-emerald-500 transition ml-auto uppercase shadow-lg shadow-emerald-900/20">Jalankan Aksi</button>
        </form>
    </div>

    <!-- 3. TABEL DATA -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50/50 text-gray-400 font-black uppercase text-[10px] tracking-widest border-b">
                    <tr>
                        <th class="px-6 py-5 text-center w-12"><input type="checkbox" id="pilih-semua" class="w-4 h-4 text-emerald-600 rounded cursor-pointer border-gray-300"></th>
                        <th class="px-6 py-5">No RM</th>
                        <th class="px-6 py-5">Identitas Pasien</th>
                        <th class="px-6 py-5">Kunjungan Terakhir</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-600">
                    @forelse($patients as $p)
                    <tr class="hover:bg-gray-50/50 transition-all group">
                        <td class="px-6 py-5 text-center">
                            <input type="checkbox" class="check-item w-4 h-4 text-emerald-600 rounded cursor-pointer border-gray-200" value="{{ $p->id }}">
                        </td>
                        <td class="px-6 py-5 font-black text-emerald-700 font-mono tracking-tighter text-base">{{ $p->no_rm }}</td>
                        <td class="px-6 py-5">
                            <div class="font-black text-gray-800 uppercase text-xs">{{ $p->nama_pasien }}</div>
                            <div class="text-[10px] text-gray-400 font-medium flex items-center gap-1 mt-1 italic">NIK: {{ $p->nik ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-5">
                            @if($p->lastVisit)
                                <div class="font-bold text-gray-700">{{ $p->lastVisit->tgl_kunjungan->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-emerald-500 font-black uppercase tracking-tighter mt-1 italic">{{ $p->lastVisit->tgl_kunjungan->diffForHumans() }}</div>
                            @else
                                <span class="text-gray-300 italic text-xs">Belum ada data</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            @php $status = $p->current_status; @endphp
                            @if($status == 'Inaktif')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black bg-yellow-50 text-yellow-700 border border-yellow-200 uppercase tracking-widest shadow-sm">
                                    📁 INAKTIF
                                </span>
                            @elseif($status == 'Siap Musnah')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black bg-red-50 text-red-700 border border-red-200 uppercase tracking-widest shadow-sm animate-pulse">
                                    🔥 SIAP MUSNAH
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            <button type="button" onclick="openRetentionModal('{{ $p->id }}', '{{ $p->nama_pasien }}', '{{ $p->no_rm }}')" 
                                class="group flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                <span class="font-black text-[10px] uppercase">Proses</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-20 text-center text-gray-400 italic">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-gray-50 bg-gray-50/30">
            {{ $patients->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- 4. MODAL PROSES RETENSI (WAJIB ENCTYPE UNTUK UPLOAD) -->
<div id="retentionModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-4" onclick="closeModalOnOuterClick(event)">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all animate-fade-in-up border border-gray-100 relative">
        
        <div class="h-24 bg-gradient-to-br from-emerald-600 to-emerald-900 relative p-6">
            <button onclick="closeRetentionModal()" class="absolute top-4 right-4 bg-black/20 text-white hover:bg-black/40 rounded-full p-1.5 transition">&times;</button>
            <div class="flex items-center gap-4 text-white">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="font-black uppercase italic text-sm tracking-widest">Verifikasi Retensi</h3>
                    <p class="text-[10px] font-bold opacity-80" id="modal-norm">RM-000000</p>
                </div>
            </div>
        </div>

        <!-- WAJIB: enctype="multipart/form-data" -->
        <form action="{{ route('retention.bulkAction') }}" method="POST" enctype="multipart/form-data" class="p-8" id="form-retensi" onsubmit="return validateRetentionForm()">
            @csrf
            <input type="hidden" name="ids" id="modal-patient-id">
            
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Pasien</label>
                    <p class="text-lg font-black text-gray-800 uppercase" id="modal-nama">Nama Pasien</p>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Status Berkas Selanjutnya</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col items-center p-4 bg-gray-50 border-2 border-transparent rounded-2xl cursor-pointer hover:bg-emerald-50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 transition-all group">
                            <input type="radio" name="action_type" value="nilai_guna" class="hidden" onchange="toggleUploadField(true)" required>
                            <svg class="w-6 h-6 text-gray-300 group-has-[:checked]:text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-[10px] font-black uppercase text-gray-500">Ada Nilai Guna</span>
                        </label>
                        <label class="relative flex flex-col items-center p-4 bg-gray-50 border-2 border-transparent rounded-2xl cursor-pointer hover:bg-red-50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50 transition-all group">
                            <input type="radio" name="action_type" value="pindahkan" class="hidden" onchange="toggleUploadField(false)">
                            <svg class="w-6 h-6 text-gray-300 group-has-[:checked]:text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span class="text-[10px] font-black uppercase text-gray-500">Siap Musnah</span>
                        </label>
                    </div>
                </div>

                <div id="upload-section" class="hidden animate-fade-in-down">
                    <div class="p-4 bg-emerald-50 rounded-2xl border border-dashed border-emerald-200">
                        <label class="text-[10px] font-black text-emerald-700 uppercase mb-3 block">Upload Nilai Guna (.PDF/.JPG)</label>
                        <input type="file" id="nilai_guna_file" name="nilai_guna_file" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-[10px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-white file:text-emerald-700 cursor-pointer">
                        <p id="error-file" class="hidden text-[9px] text-red-600 font-bold uppercase mt-2 italic">⚠️ File wajib diunggah!</p>
                    </div>
                </div>

                <div id="notice-section" class="hidden animate-fade-in-down">
                    <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-[10px] text-amber-800 font-bold uppercase leading-relaxed">Peringatan: Berkas tanpa nilai guna akan dipindahkan ke daftar pemusnahan.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t flex gap-3">
                <button type="button" onclick="closeRetentionModal()" class="flex-1 py-3 bg-gray-100 text-gray-600 font-black rounded-xl text-[10px] uppercase">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-emerald-600 text-white font-black rounded-xl text-[10px] uppercase shadow-lg shadow-emerald-200 active:scale-95 transition-transform">Konfirmasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRetentionModal(id, nama, norm) {
        document.getElementById('modal-patient-id').value = id;
        document.getElementById('modal-nama').innerText = nama;
        document.getElementById('modal-norm').innerText = 'RM: ' + norm;
        
        // Reset
        const radios = document.getElementsByName('action_type');
        radios.forEach(r => r.checked = false);
        document.getElementById('upload-section').classList.add('hidden');
        document.getElementById('notice-section').classList.add('hidden');
        document.getElementById('error-file').classList.add('hidden');
        document.getElementById('nilai_guna_file').value = '';

        document.getElementById('retentionModal').classList.remove('hidden');
    }

    function toggleUploadField(show) {
        const upload = document.getElementById('upload-section');
        const notice = document.getElementById('notice-section');
        const fileInput = document.getElementById('nilai_guna_file');
        
        if(show) {
            upload.classList.remove('hidden');
            notice.classList.add('hidden');
            fileInput.required = true;
        } else {
            upload.classList.add('hidden');
            notice.classList.remove('hidden');
            fileInput.required = false;
        }
    }

    function validateRetentionForm() {
        const actionType = document.querySelector('input[name="action_type"]:checked');
        const fileInput = document.getElementById('nilai_guna_file');
        const errorMsg = document.getElementById('error-file');

        if (actionType && actionType.value === 'nilai_guna') {
            if (fileInput.files.length === 0) {
                errorMsg.classList.remove('hidden');
                return false; 
            }
        }
        return true;
    }

    function closeRetentionModal() {
        document.getElementById('retentionModal').classList.add('hidden');
    }

    function closeModalOnOuterClick(event) {
        if (event.target.id === 'retentionModal') closeRetentionModal();
    }

    function toggleSort() {
        const input = document.getElementById('sort_order');
        input.value = input.value === 'desc' ? 'asc' : 'desc';
        document.getElementById('filter-form').submit();
    }
</script>

<style>
    @keyframes fade-in-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fade-in-down { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fade-in-up 0.3s ease-out forwards; }
    .animate-fade-in-down { animation: fade-in-down 0.2s ease-out forwards; }
</style>
@endsection