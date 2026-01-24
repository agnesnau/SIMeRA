@extends('layouts.app')
@section('title', 'Manajemen Data Kunjungan')

@section('content')
<div class="space-y-6">
    
    <!-- 1. HEADER & SEARCH DENGAN FILTER WAKTU & SORTING -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('visits.index') }}" method="GET" id="filter-form" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <!-- Pencarian Teks -->
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No RM..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 w-full text-sm outline-none transition-all">
            </div>

            <!-- Filter Waktu -->
            <select name="filter_time" onchange="this.form.submit()" class="py-2 pl-3 pr-8 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white cursor-pointer outline-none font-medium">
                <option value="">Semua Waktu</option>
                <option value="minggu" {{ request('filter_time') == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="3_bulan" {{ request('filter_time') == '3_bulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                <option value="6_bulan" {{ request('filter_time') == '6_bulan' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                <option value="1_tahun" {{ request('filter_time') == '1_tahun' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                <option value="2_tahun" {{ request('filter_time') == '2_tahun' ? 'selected' : '' }}>2 Tahun Terakhir</option>
                <option value="lebih_2_tahun" {{ request('filter_time') == 'lebih_2_tahun' ? 'selected' : '' }}>Lebih dari 2 Tahun</option>
            </select>

            <!-- Tombol Urutan (Sorting) -->
            <input type="hidden" name="sort_order" id="sort_order" value="{{ request('sort_order', 'desc') }}">
            <button type="button" onclick="toggleSort()" class="p-2 border border-gray-300 rounded-lg bg-gray-50 hover:bg-emerald-50 hover:border-emerald-200 transition-all flex items-center gap-2 text-sm font-bold text-gray-600">
                @if(request('sort_order', 'desc') === 'desc')
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                    <span>Terbaru</span>
                @else
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-1l4 4m0 0l4-4m-4 4V4"></path></svg>
                    <span>Terlama</span>
                @endif
            </button>
        </form>

        @if(strtolower(auth()->user()->level) !== 'kepala')
        <a href="{{ route('visits.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm font-bold uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Kunjungan Baru
        </a>
        @endif
    </div>

    <!-- 2. BULK ACTION BAR -->
    <div id="bulk-action-area" class="hidden animate-fade-in-down">
        <form action="{{ route('visits.bulkAction') }}" method="POST" class="bg-gray-800 p-4 rounded-xl flex items-center gap-4 shadow-lg text-white border border-gray-700">
            @csrf
            <input type="hidden" name="ids" id="input-ids-terpilih">
            <div class="text-sm font-bold">
                <span class="bg-emerald-500 px-2 py-0.5 rounded text-white mr-2" id="jumlah-terpilih">0</span> Data Terpilih
            </div>
            <select name="action_type" class="p-2 rounded-lg text-sm bg-gray-700 text-white border border-gray-600 outline-none cursor-pointer font-medium" required>
                <option value="">-- Pilih Tindakan --</option>
                <option value="hapus">🗑️ Hapus Permanen</option>
            </select>
            <button type="submit" onclick="return confirm('Hapus data terpilih?')" class="bg-white text-gray-900 px-6 py-2 rounded-lg font-black text-sm hover:bg-emerald-50 transition ml-auto uppercase">Eksekusi</button>
        </form>
    </div>

    <!-- 3. TABEL KUNJUNGAN -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-bold border-b-2 border-gray-200">
                    <tr>
                        @if(strtolower(auth()->user()->level) !== 'kepala')
                        <th class="px-6 py-4 text-center w-12"><input type="checkbox" id="pilih-semua" class="w-4 h-4 text-emerald-600 rounded cursor-pointer"></th>
                        @endif
                        <th class="px-6 py-4">No Registrasi</th>
                        <th class="px-6 py-4">Identitas Pasien</th>
                        <th class="px-6 py-4">Tgl Kunjungan</th>
                        <th class="px-6 py-4">Poli / Dokter</th>
                        <th class="px-6 py-4 text-center">Status Retensi</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($visits as $v)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        @if(strtolower(auth()->user()->level) !== 'kepala')
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" class="check-visit w-4 h-4 text-emerald-600 rounded cursor-pointer" value="{{ $v->id }}">
                        </td>
                        @endif
                        <td class="px-6 py-4 font-bold text-emerald-700 font-mono italic">{{ $v->no_registrasi }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 uppercase">{{ $v->patient->nama_pasien ?? 'Pasien Terhapus' }}</div>
                            <div class="text-[10px] text-gray-400 font-mono tracking-tighter">RM: {{ $v->patient->no_rm ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $v->tgl_kunjungan->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-gray-400 italic">{{ $v->tgl_kunjungan->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-blue-700 uppercase">{{ $v->poli ?? 'Poli Umum' }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-medium">dr. {{ $v->dokter }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php $status = $v->patient->current_status ?? 'Aktif'; @endphp
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
                                <button type="button" 
                                    onclick='openDetailModal(@json($v))' 
                                    class="p-2 bg-gray-50 text-gray-500 rounded-lg hover:bg-emerald-50 hover:text-emerald-600 border border-gray-200 transition"
                                    title="Lihat Riwayat & Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                @if(strtolower(auth()->user()->level) !== 'kepala')
                                <a href="{{ route('visits.edit', $v->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 border border-blue-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <form action="{{ route('visits.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Hapus kunjungan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-200 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400 italic bg-gray-50/50">Belum ada riwayat kunjungan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50">{{ $visits->links() }}</div>
    </div>

    <!-- 4. MODAL DETAIL KUNJUNGAN & RIWAYAT -->
    <div id="detailModal" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-4" onclick="closeModal(event)">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in-up border border-gray-100 flex flex-col max-h-[90vh]">
            <!-- Header Section -->
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-900 p-8 relative shrink-0">
                <button type="button" onclick="document.getElementById('detailModal').classList.add('hidden')" class="absolute top-4 right-4 bg-white/10 text-white rounded-full p-2 hover:bg-white/30 transition">&times;</button>
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center text-emerald-700 shadow-xl border-4 border-emerald-500/20">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-black text-white uppercase tracking-tight" id="dv-nama">Nama Pasien</h2>
                            <div id="dv-status-badge"></div>
                        </div>
                        <p class="text-emerald-100 text-sm font-bold opacity-80" id="dv-norm">RM: 000000</p>
                    </div>
                </div>
            </div>

            <!-- Content Section (Scrollable) -->
            <div class="p-8 overflow-y-auto">
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Dokter Terakhir</p>
                        <p class="text-sm font-bold text-gray-700" id="dv-dokter">-</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Poli Pelayanan</p>
                        <p class="text-sm font-bold text-gray-700" id="dv-poli">-</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Kunjungan Pasien
                    </h3>
                    <div class="border rounded-2xl overflow-hidden bg-white">
                        <table class="w-full text-[11px] text-left">
                            <thead class="bg-gray-50 text-gray-500 font-bold border-b">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">No. Reg</th>
                                    <th class="px-4 py-3">Poli</th>
                                    <th class="px-4 py-3">Dokter</th>
                                </tr>
                            </thead>
                            <tbody id="history-body" class="divide-y text-gray-600"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-end shrink-0">
                <button type="button" onclick="document.getElementById('detailModal').classList.add('hidden')" class="px-8 py-2.5 bg-gray-200 text-gray-700 font-black rounded-xl text-xs hover:bg-gray-300 transition uppercase">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. FUNGSI SORTING GLOBAL
    function toggleSort() {
        const input = document.getElementById('sort_order');
        const form = document.getElementById('filter-form');
        if (input && form) {
            input.value = input.value === 'desc' ? 'asc' : 'desc';
            form.submit();
        }
    }

    // 2. FUNGSI MODAL DETAIL GLOBAL
    function openDetailModal(visit) {
        const patient = visit.patient || {};
        const status = patient.current_status || 'Aktif';
        
        document.getElementById('dv-nama').innerText = patient.nama_pasien || 'Pasien Terhapus';
        document.getElementById('dv-norm').innerText = 'RM: ' + (patient.no_rm || '-');
        document.getElementById('dv-dokter').innerText = 'dr. ' + visit.dokter;
        document.getElementById('dv-poli').innerText = visit.poli || 'Poli Umum';

        const badgeContainer = document.getElementById('dv-status-badge');
        let badgeClass = "bg-white/20 text-white border-white/40";
        if(status === 'Aktif') badgeClass = "bg-green-500/20 text-green-100 border-green-400/30";
        else if(status === 'Inaktif') badgeClass = "bg-yellow-500/20 text-yellow-100 border-yellow-400/30";
        else if(status === 'Siap Musnah') badgeClass = "bg-red-500/20 text-red-100 border-red-400/30";

        badgeContainer.innerHTML = `<span class="px-3 py-0.5 rounded-full text-[10px] font-black uppercase border ${badgeClass}">${status}</span>`;

        const historyBody = document.getElementById('history-body');
        historyBody.innerHTML = '';

        if (patient.visits && patient.visits.length > 0) {
            patient.visits.forEach(h => {
                const date = new Date(h.tgl_kunjungan);
                const formattedDate = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                const row = `
                    <tr class="${h.id === visit.id ? 'bg-emerald-50 font-bold' : ''} hover:bg-gray-50 transition">
                        <td class="px-4 py-3">${formattedDate}</td>
                        <td class="px-4 py-3 font-mono text-gray-400">${h.no_registrasi}</td>
                        <td class="px-4 py-3 uppercase text-blue-600">${h.poli || 'UMUM'}</td>
                        <td class="px-4 py-3 uppercase italic">dr. ${h.dokter}</td>
                    </tr>
                `;
                historyBody.innerHTML += row;
            });
        } else {
            historyBody.innerHTML = '<tr><td colspan="4" class="p-4 text-center italic text-gray-400">Tidak ada riwayat kunjungan.</td></tr>';
        }

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModal(e) {
        if(e.target.id === 'detailModal') {
            document.getElementById('detailModal').classList.add('hidden');
        }
    }

    // 3. LOGIKA DOM READY UNTUK CHECKBOX
    document.addEventListener('DOMContentLoaded', function() {
        const pilihSemua = document.getElementById('pilih-semua');
        const checkboxes = document.querySelectorAll('.check-visit');
        const bulkArea = document.getElementById('bulk-action-area');
        const labelJumlah = document.getElementById('jumlah-terpilih');
        const inputIds = document.getElementById('input-ids-terpilih');

        if(pilihSemua) {
            pilihSemua.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulk();
            });
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateBulk));

        function updateBulk() {
            const terpilih = Array.from(document.querySelectorAll('.check-visit:checked')).map(cb => cb.value);
            if(inputIds) inputIds.value = terpilih.join(',');
            if(labelJumlah) labelJumlah.innerText = terpilih.length;
            if(bulkArea) terpilih.length > 0 ? bulkArea.classList.remove('hidden') : bulkArea.classList.add('hidden');
        }
    });
</script>
@endsection