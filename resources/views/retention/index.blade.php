@extends('layouts.app')
@section('title', 'Manajemen Retensi & Penilaian Arsip')

@section('content')
<div class="space-y-6 animate-in fade-in duration-700" x-data="{ appraisalChoice: 'none', fileSelected: false }">
    
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-5 rounded-2xl shadow-sm flex items-center gap-4 animate-in slide-in-from-top-4 duration-500">
        <div class="w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div>
            <p class="text-xs font-black text-emerald-900 uppercase tracking-widest leading-none mb-1">Berhasil</p>
            <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-5 rounded-2xl shadow-sm flex items-center gap-4 animate-in slide-in-from-top-4 duration-500">
        <div class="w-10 h-10 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-red-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-black text-red-900 uppercase tracking-widest leading-none mb-1">Terjadi Kesalahan</p>
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    @endif

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
        <form action="{{ route('retensi.index') }}" method="GET" id="filter-form" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No RM..." 
                    class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-sm outline-none transition-all">
            </div>

            <input type="hidden" name="sort" id="sort_direction" value="{{ request('sort', 'desc') }}">
            <button type="button" onclick="toggleSort()" class="px-5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-3 hover:bg-slate-100 transition-all text-sm font-black text-slate-600 shadow-sm group">
                @if(request('sort', 'desc') == 'desc')
                    <svg class="w-5 h-5 text-emerald-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" />
                    </svg>
                    <span>Kunjungan Terbaru</span>
                @else
                    <svg class="w-5 h-5 text-blue-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9m5.25-6L21 11.25m0 0l-3.75 3.75M21 11.25V3" />
                    </svg>
                    <span>Kunjungan Terlama</span>
                @endif
            </button>
        </form>
    </div>

    @if(auth()->user()->level !== 'supervisor')
    <div id="bulk-action-area" class="hidden">
        <form action="{{ route('retensi.bulkAction') }}" method="POST" id="form-massal" class="bg-gray-800 p-4 rounded-xl flex flex-wrap gap-4 items-center shadow-lg text-white border border-gray-700">
            @csrf
            <input type="hidden" name="ids" id="input-ids-terpilih">
            <div class="text-sm font-bold flex items-center gap-2">
                <span class="bg-gray-700 px-3 py-1 rounded-full text-xs text-white border border-gray-600" id="jumlah-terpilih">0</span> 
                <span>Data Terpilih</span>
            </div>
            <div class="h-6 w-px bg-gray-600 mx-2"></div>
            <select name="action_type" class="p-2 rounded-lg text-sm bg-gray-700 text-white border border-gray-600 outline-none font-medium" required>
                <option value="">-- Pilih Tindakan --</option>
                <option value="pindah">📦 Pindah ke Gudang (Pemilahan)</option>
                <option value="hapus">🗑️ Hapus Data Permanen</option>
            </select>
            <button type="submit" onclick="return confirm('Proses data terpilih?')" class="bg-white text-gray-900 px-6 py-2 rounded-lg font-black text-sm hover:bg-emerald-50 transition ml-auto active:scale-95">PROSES</button>
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
                            <input type="checkbox" id="check-all" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        </th>
                        @endif
                        <th class="px-8 py-5">Informasi Berkas (No RM)</th>
                        <th class="px-8 py-5">Kunjungan Terakhir</th>
                        <th class="px-8 py-5 text-center">Estimasi Status</th>
                        <th class="px-8 py-5 text-center">Aksi Pemindahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paginatedPatients as $p)
                        @php 
                            $years = $p->calculated_years ?? ($p->lastVisit ? $p->lastVisit->tgl_kunjungan->diffInYears(now()) : 0);
                        @endphp
                        
                        {{-- UPDATE: FILTER HANYA TAMPILKAN 2-4 TAHUN (INAKTIF SAJA) --}}
                        @if($years >= 2 && $years < 4 && !$p->manual_status)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            @if(auth()->user()->level !== 'supervisor')
                            <td class="px-8 py-6 text-center">
                                <input type="checkbox" class="check-item w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" value="{{ $p->id }}">
                            </td>
                            @endif
                            <td class="px-8 py-6">
                                <div class="font-black text-slate-800 uppercase text-base">{{ $p->nama_pasien }}</div>
                                <div class="text-[11px] font-bold text-blue-600 font-mono mt-1 tracking-tighter">INDEX: {{ $p->no_rm }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-600">{{ $p->lastVisit ? $p->lastVisit->tgl_kunjungan->format('d/m/Y') : '-' }}</div>
                                <div class="text-[10px] text-slate-400 mt-1 uppercase font-black tracking-tighter">Umur Berkas: {{ $years }} Tahun</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-[10px] font-black uppercase tracking-widest">INAKTIF</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <form action="{{ route('retensi.sendToSorting', $p->id) }}" method="POST" onsubmit="return confirm('Pindahkan berkas fisik ke Meja Pemilahan di Gudang?')">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 px-6 py-2.5 bg-slate-800 text-white rounded-xl text-[10px] font-black hover:bg-emerald-600 transition shadow-lg hover:shadow-emerald-200 uppercase tracking-widest mx-auto active:scale-95 group/btn">
                                        <svg class="w-4 h-4 text-emerald-400 group-hover/btn:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        Pindah ke Gudang
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endif
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 border-2 border-dashed border-slate-100">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <p class="text-slate-400 text-xs font-bold uppercase italic tracking-widest">Semua rak inaktif (2-4 tahun) sudah bersih.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-gray-50 border-t border-gray-100">{{ $paginatedPatients->links() }}</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. SCRIPT SORTIR ---
        window.toggleSort = function() {
            const input = document.getElementById('sort_direction');
            const form = document.getElementById('filter-form');
            if (input && form) {
                input.value = input.value === 'desc' ? 'asc' : 'desc';
                form.submit();
            }
        }

        // --- 2. SCRIPT CHECKBOX & BULK ACTION ---
        const checkAll = document.getElementById('check-all');
        const checkItems = document.querySelectorAll('.check-item');
        const bulkArea = document.getElementById('bulk-action-area');
        const totalBadge = document.getElementById('jumlah-terpilih');
        const inputIds = document.getElementById('input-ids-terpilih');

        function updateBulkAction() {
            const checkedBoxes = document.querySelectorAll('.check-item:checked');
            const totalChecked = checkedBoxes.length;

            // Gabungkan ID ke input hidden
            const ids = Array.from(checkedBoxes).map(cb => cb.value).join(',');
            if(inputIds) inputIds.value = ids;
            if(totalBadge) totalBadge.innerText = totalChecked;

            // Munculkan/Sembunyikan Area Aksi
            if (bulkArea) {
                if (totalChecked > 0) {
                    bulkArea.classList.remove('hidden');
                } else {
                    bulkArea.classList.add('hidden');
                }
            }
        }

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                checkItems.forEach(cb => cb.checked = this.checked);
                updateBulkAction();
            });
        }

        checkItems.forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked && checkAll) checkAll.checked = false;
                updateBulkAction();
            });
        });
    });
</script>
@endsection