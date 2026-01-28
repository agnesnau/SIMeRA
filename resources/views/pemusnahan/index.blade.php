@extends('layouts.app')
@section('title', 'Penilaian & Verifikasi Pemusnahan')

@section('content')
<div class="space-y-6 animate-in fade-in duration-700" x-data="{ appraisalChoice: 'none', fileSelected: false }">
    
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-5 rounded-2xl shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div>
            <p class="text-xs font-black text-emerald-900 uppercase tracking-widest leading-none mb-1">Berhasil</p>
            <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg></button>
    </div>
    @endif

    
    <!-- 2. HEADER SOP (TIDAK BERUBAH) -->
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-full -mr-32 -mt-32 opacity-50"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2 bg-emerald-600 text-white rounded-xl shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">SOP Retensi & Siklus Hidup Berkas</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Rule Pengelolaan Rekam Medis Puskesmas Silo 1</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                <div class="hidden lg:block absolute top-1/2 left-0 w-full h-0.5 bg-slate-100 -translate-y-1/2 z-0"></div>

                <div class="relative z-10 bg-white p-5 rounded-2xl border-2 border-emerald-500 shadow-lg group hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-emerald-600 text-white rounded-lg flex items-center justify-center font-black">1</div>
                        <h4 class="text-[9px] font-black text-emerald-700 uppercase tracking-widest leading-tight">Masa Aktif<br>(Pelayanan)</h4>
                    </div>
                    <p class="text-[9px] text-slate-500 leading-relaxed mb-4">Masa kunjungan < 2 tahun. Berkas berada pada Rak Aktif.</p>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center font-black">
                        <span class="text-[8px] text-slate-400 uppercase">Aktif:</span>
                        <span class="text-xs text-emerald-600">{{ $stats['total_aktif'] ?? 0 }} Berkas</span>
                    </div>
                </div>

                <div class="relative z-10 bg-white p-5 rounded-2xl border-2 border-blue-400 shadow-md group hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center font-black">2</div>
                        <h4 class="text-[9px] font-black text-blue-700 uppercase tracking-widest leading-tight">Aktif ke Inaktif<br>(Pemilahan)</h4>
                    </div>
                    <p class="text-[9px] text-slate-500 leading-relaxed mb-4">Proses verifikasi fisik dan pemindahan berkas.</p>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center font-black text-blue-600">
                        <span class="text-[8px] text-slate-400 uppercase">Inaktif:</span>      
                        <span class="text-xs text-amber-600">{{ $stats['total_inaktif'] ?? 0 }} Berkas</span>
                    </div>
                </div>

                <div class="relative z-10 bg-white p-5 rounded-2xl border-2 border-amber-400 shadow-md group hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-amber-500 text-white rounded-lg flex items-center justify-center font-black">3</div>
                        <h4 class="text-[9px] font-black text-amber-700 uppercase tracking-widest leading-tight">Inaktif<br>(Penilaian)</h4>
                    </div>
                    <p class="text-[9px] text-slate-500 leading-relaxed mb-4">Masa simpan 2-4 tahun. Penilaian lembar bernilai guna.</p>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center font-black">
                        <span class="text-[8px] text-slate-400 uppercase tracking-tighter">Status Transisi</span>
                        <span class="text-xs text-amber-600">{{ $stats['total_pemilahan'] ?? 0 }} Berkas</span>
                    </div>
                </div>
                
                <div class="relative z-10 bg-white p-5 rounded-2xl border-2 border-red-500 shadow-md group hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-red-600 text-white rounded-lg flex items-center justify-center font-black">4</div>
                        <h4 class="text-[9px] font-black text-red-700 uppercase tracking-widest leading-tight">Siap Musnah<br>(Eksekusi)</h4>
                    </div>
                    <p class="text-[9px] text-slate-500 leading-relaxed mb-4">Masa simpan > 4 tahun. Siap dimusnahkan.</p>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center font-black">
                        <span class="text-[8px] text-slate-400 uppercase">Siap Musnah:</span>
                        <span class="text-xs text-red-600">{{ $stats['siap_musnah'] ?? 0 }} Berkas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('pemusnahan.index') }}" method="GET" id="filter-form" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No RM..." 
                    class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 w-full text-sm outline-none transition-all">
            </div>

            <input type="hidden" name="sort" id="sort_direction" value="{{ request('sort', 'desc') }}">
            <a href="{{ request()->fullUrlWithQuery(['sort' => (request('sort', 'desc') == 'desc' ? 'asc' : 'desc')]) }}" 
               class="px-5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-3 hover:bg-slate-100 transition-all text-sm font-black text-slate-600 shadow-sm group text-decoration-none">
                
                @if(request('sort', 'desc') == 'desc')
                    <svg class="w-5 h-5 text-indigo-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" /></svg>
                    <span>Terbaru</span>
                @else
                    <svg class="w-5 h-5 text-red-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9m5.25-6L21 11.25m0 0l-3.75 3.75M21 11.25V3" /></svg>
                    <span>Terlama</span>
                @endif
            </a>
        </form>
    </div>

    @if(auth()->user()->level !== 'supervisor')
    <div id="bulk-action-area" class="hidden">
        <form action="{{ route('pemusnahan.bulkAction') }}" method="POST" id="form-massal" class="bg-slate-800 p-4 rounded-xl flex flex-wrap gap-4 items-center shadow-lg text-white border border-slate-700">
            @csrf
            <input type="hidden" name="ids" id="input-ids-terpilih">
            <div class="text-sm font-bold flex items-center gap-2">
                <span class="bg-slate-600 px-3 py-1 rounded-full text-xs text-white border border-slate-500" id="jumlah-terpilih">0</span> 
                <span>Terpilih</span>
            </div>
            <div class="h-6 w-px bg-slate-600 mx-2"></div>
            
            <select name="action_type" class="p-2 rounded-lg text-sm bg-slate-700 text-white border border-slate-600 outline-none font-medium focus:ring-2 focus:ring-indigo-500" required>
                <option value="">-- Pilih Keputusan --</option>
                <option value="siap_musnah">🔥 Tandai SIAP MUSNAH</option>
                <option value="abadi">🛡️ Simpan ARSIP ABADI</option>
            </select>
            
            <button type="submit" onclick="return confirm('Proses penilaian massal?')" class="bg-white text-slate-900 px-6 py-2 rounded-lg font-black text-sm hover:bg-indigo-50 transition ml-auto active:scale-95">PROSES</button>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-gray-400 font-bold border-b text-[10px] uppercase tracking-widest">
                    <tr>
                        @if(auth()->user()->level !== 'supervisor')
                        <th class="px-8 py-5 w-10 text-center">
                            <input type="checkbox" id="check-all" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        @endif
                        <th class="px-8 py-5">Informasi Berkas (No RM)</th>
                        <th class="px-8 py-5">Pasien</th>
                        <th class="px-8 py-5 text-center">Masa Inaktif</th>
                        <th class="px-8 py-5 text-center">Aksi Penilaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($candidates as $p)
                        <tr class="hover:bg-indigo-50/10 transition-colors group">
                            @if(auth()->user()->level !== 'supervisor')
                            <td class="px-8 py-6 text-center">
                                <input type="checkbox" class="check-item w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" value="{{ $p->id }}">
                            </td>
                            @endif
                            <td class="px-8 py-6">
                                <div class="font-mono font-bold text-indigo-600 text-base">{{ $p->no_rm }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-black text-slate-800 uppercase">{{ $p->nama_pasien }}</div>
                                <div class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-tight">
                                    Terakhir: {{ $p->lastVisit ? $p->lastVisit->tgl_kunjungan->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-200">
                                    {{ $p->lastVisit ? $p->lastVisit->tgl_kunjungan->diffInYears(now()) : '-' }} Tahun
                                </span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <button onclick="openModal('{{ $p->id }}', '{{ $p->nama_pasien }}')" 
                                    class="flex items-center gap-2 px-5 py-2.5 bg-slate-800 text-white rounded-xl text-[10px] font-black hover:bg-indigo-600 transition shadow-lg hover:shadow-indigo-200 uppercase tracking-widest mx-auto active:scale-95 group/btn">
                                    <svg class="w-4 h-4 text-amber-400 group-hover/btn:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Nilai
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 border-2 border-dashed border-slate-100">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-slate-400 text-xs font-bold uppercase italic tracking-widest">
                                        Tidak ada data yang menunggu penilaian.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-gray-50 border-t border-gray-100">{{ $candidates->links() }}</div>
    </div>
</div>

{{-- MODAL --}}
{{-- MODAL PENILAIAN (UPDATE: SIMPEL + UPLOAD) --}}
<div id="modalAppraisal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white p-8 rounded-3xl shadow-2xl">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-xl font-black uppercase text-slate-800 tracking-tight">Penilaian Nilai Guna</h3>
                <p class="text-xs text-slate-400 mt-1 font-medium">Pasien: <span id="modalName" class="font-bold text-indigo-600"></span></p>
            </div>
            <button onclick="closeModal()" class="text-slate-300 hover:text-red-500 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>

        <form id="formAppraisal" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2 tracking-widest">Keputusan Nilai Guna</label>
                <div class="relative">
                    <select name="nilai_guna" id="selectNilaiGuna" onchange="toggleUpload()" class="w-full p-4 border border-gray-200 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-indigo-500 appearance-none bg-slate-50 font-bold text-slate-700">
                        <option value="none" class="text-red-600">❌ TIDAK ADA (Lanjut Musnahkan)</option>
                        <option value="bernilai" class="text-emerald-600">✅ ADA NILAI GUNA (Simpan Abadi)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                </div>
            </div>

            <div id="uploadContainer" class="hidden mb-6 animate-in slide-in-from-top-2 duration-300">
                <label class="block text-xs font-bold uppercase text-emerald-600 mb-2 tracking-widest">
                    Wajib Upload Bukti / Berkas Digital *
                </label>
                <div class="border-2 border-dashed border-emerald-200 rounded-xl p-4 bg-emerald-50 text-center hover:bg-emerald-100 transition relative">
                    <input type="file" name="file_nilai_guna" id="fileInput" onchange="checkFile()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="text-emerald-600 pointer-events-none">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <span id="fileNameDisplay" class="text-xs font-bold">Klik untuk Upload File</span>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 mt-2 text-center">Format: PDF/JPG (Max 5MB)</p>
            </div>

            <button type="submit" id="btnSubmit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-indigo-700 shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                Simpan Keputusan
            </button>
        </form>
    </div>
</div>

<script>
    // --- LOGIKA JAVASCRIPT MODAL ---
    function openModal(id, name) {
        document.getElementById('modalName').innerText = name;
        document.getElementById('formAppraisal').action = "/pemusnahan/" + id + "/assess";
        
        // Reset Form saat dibuka
        document.getElementById('selectNilaiGuna').value = 'none';
        document.getElementById('fileInput').value = '';
        document.getElementById('fileNameDisplay').innerText = 'Klik untuk Upload File';
        
        toggleUpload(); // Reset tampilan
        document.getElementById('modalAppraisal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modalAppraisal').classList.add('hidden');
    }

    function toggleUpload() {
        const val = document.getElementById('selectNilaiGuna').value;
        const uploadDiv = document.getElementById('uploadContainer');
        const btn = document.getElementById('btnSubmit');

        if (val === 'bernilai') {
            // Kalau pilih ADA NILAI -> Tampilkan Upload, Matikan Tombol Submit
            uploadDiv.classList.remove('hidden');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-slate-400');
            btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
        } else {
            // Kalau pilih TIDAK ADA -> Sembunyikan Upload, Nyalakan Tombol Submit
            uploadDiv.classList.add('hidden');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-slate-400');
            btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
        }
    }

    function checkFile() {
        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const btn = document.getElementById('btnSubmit');

        if (fileInput.files.length > 0) {
            // Kalau ada file -> Tampilkan nama file & Nyalakan tombol
            fileNameDisplay.innerText = fileInput.files[0].name;
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-slate-400');
            btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
        } else {
            fileNameDisplay.innerText = 'Klik untuk Upload File';
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-slate-400');
        }
    }
</script>
<script>
    // ... fungsi toggleSort & openModal sama ...

    function toggleUpload() {
        const val = document.getElementById('selectNilaiGuna').value;
        const uploadDiv = document.getElementById('uploadContainer');
        const btn = document.getElementById('btnSubmit');

        if (val === 'bernilai') {
            // JIKA ADA NILAI GUNA
            uploadDiv.classList.remove('hidden');
            
            // Ubah Text Tombol
            btn.innerText = "PINDAHKAN KE EKSEKUSI (ARSIP DIGITAL)";
            
            // Ganti Warna jadi Hijau (Tanda Aman)
            btn.classList.remove('bg-red-600', 'hover:bg-red-700');
            btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');

            checkFile(); // Cek file dulu sebelum nyalain tombol
        } else {
            // JIKA TIDAK ADA
            uploadDiv.classList.add('hidden');
            
            // Ubah Text Tombol
            btn.innerText = "PINDAHKAN KE EKSEKUSI (MUSNAH TOTAL)";
            
            // Ganti Warna jadi Merah (Tanda Bahaya)
            btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'opacity-50', 'cursor-not-allowed');
            btn.classList.add('bg-red-600', 'hover:bg-red-700');
            
            btn.disabled = false;
        }
    }

    function checkFile() {
        const val = document.getElementById('selectNilaiGuna').value;
        if(val !== 'bernilai') return;

        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const btn = document.getElementById('btnSubmit');

        if (fileInput.files.length > 0) {
            fileNameDisplay.innerText = fileInput.files[0].name;
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-slate-400');
            btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
        } else {
            fileNameDisplay.innerText = 'Klik untuk Upload File';
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-slate-400');
        }
    }
</script>
@endsection