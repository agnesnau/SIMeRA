@extends('layouts.app')
@section('title', 'Manajemen Data Kunjungan')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        
        <form action="{{ route('visits.index') }}" method="GET" id="filter-form" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No RM..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 w-full text-sm outline-none transition-all">
            </div>

            <select name="filter_time" onchange="this.form.submit()" class="py-2 pl-3 pr-8 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white cursor-pointer outline-none font-medium">
                <option value="">Semua Waktu</option>
                <option value="minggu" {{ request('filter_time') == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="3_bulan" {{ request('filter_time') == '3_bulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                <option value="6_bulan" {{ request('filter_time') == '6_bulan' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                <option value="1_tahun" {{ request('filter_time') == '1_tahun' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                <option value="2_tahun" {{ request('filter_time') == '2_tahun' ? 'selected' : '' }}>2 Tahun Terakhir</option>
                <option value="lebih_2_tahun" {{ request('filter_time') == 'lebih_2_tahun' ? 'selected' : '' }}>Lebih dari 2 Tahun</option>
            </select>

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

        @if(auth()->user()->level !== 'supervisor')
        <a href="{{ route('visits.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm font-bold uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Kunjungan Baru
        </a>
        @endif
    </div>

    @if(auth()->user()->level !== 'supervisor')
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
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-bold border-b-2 border-gray-200">
                    <tr>
                        @if(auth()->user()->level !== 'supervisor')
                        <th class="px-6 py-4 text-center w-12"><input type="checkbox" id="pilih-semua" class="w-4 h-4 text-emerald-600 rounded cursor-pointer"></th>
                        @endif
                        
                        <th class="px-6 py-4">No RM</th>
                        <th class="px-6 py-4">Identitas Pasien</th>
                        <th class="px-6 py-4">Tgl Kunjungan</th>
                        <th class="px-6 py-4">Poli / Dokter</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($visits as $v)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        
                        @if(auth()->user()->level !== 'supervisor')
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" class="check-visit w-4 h-4 text-emerald-600 rounded cursor-pointer" value="{{ $v->id }}">
                        </td>
                        @endif

                        <td class="px-6 py-4 font-bold text-emerald-700 font-mono italic">{{ $v->patient->no_rm }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 uppercase">{{ $v->patient->nama_pasien ?? 'Pasien Terhapus' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $v->tgl_kunjungan->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-gray-400 italic">{{ $v->tgl_kunjungan->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-blue-700 uppercase">{{ $v->poli_tujuan ?? 'Poli Umum' }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-medium">dr. {{ $v->dokter }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php $status = $v->patient->current_status ?? 'Aktif'; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wide
                                {{ $status == 'Aktif' ? 'bg-green-100 text-green-800 border-green-200' : ($status == 'Inaktif' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 'bg-red-100 text-red-800 border-red-200') }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center gap-2">
                                
                                <a href="{{ route('visits.show', $v->id) }}" 
                                   class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg border border-slate-200 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all active:scale-95 shadow-sm group"
                                   title="Buka Lembar Medis">
                                    <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    <span class="text-[10px] font-black uppercase tracking-tighter">Lihat Detail</span>
                                </a>

                                @if(auth()->user()->level !== 'supervisor')
                                <a href="{{ route('visits.edit', $v->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Riwayat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <form action="{{ route('visits.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat kunjungan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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

    // 2. LOGIKA DOM READY UNTUK CHECKBOX & BULK ACTION
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