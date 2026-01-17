@extends('layouts.app')
@section('title', 'Pemusnahan Rekam Medis')

@section('content')
<div class="space-y-8">

    <!-- ALERT INFO -->
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm flex items-start gap-3">
        <svg class="w-6 h-6 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div>
            <h4 class="font-bold text-red-800">Area Pemusnahan</h4>
            <p class="text-sm text-red-700 mt-1">
                Data di bawah ini adalah berkas yang <strong>sudah disetujui (Retensi)</strong>. <br>
                Pastikan Berita Acara sudah dicetak sebelum menekan tombol "Musnahkan".
            </p>
        </div>
    </div>

    <!-- TABEL SIAP MUSNAH -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-white flex justify-between items-center">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Daftar Siap Eksekusi
            </h3>
            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">{{ $readyToDestroy->count() }} Berkas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-red-50 text-red-900 font-bold border-b border-red-100">
                    <tr>
                        <th class="px-6 py-4">No RM</th>
                        <th class="px-6 py-4">Pasien</th>
                        <th class="px-6 py-4">Kunjungan Terakhir</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($readyToDestroy as $p)
                    <tr class="hover:bg-red-50/30 transition-colors">
                        <td class="px-6 py-4 font-bold font-mono">{{ $p->no_rm }}</td>
                        <td class="px-6 py-4 font-medium">{{ $p->nama_pasien }}</td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $p->lastVisit ? $p->lastVisit->tgl_kunjungan->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">SIAP MUSNAH</span>
                        </td>
                        <td class="px-6 py-4 text-center flex justify-center gap-3">
                            <!-- Tombol Restore -->
                            <form action="{{ route('pemusnahan.restore', $p->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-xs font-medium underline py-2">
                                    Batalkan (Restore)
                                </button>
                            </form>

                            <!-- Tombol Eksekusi -->
                            <form action="{{ route('pemusnahan.execute', $p->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin fisik berkas ini SUDAH DIHANCURKAN? Status akan berubah permanen.');">
                                @csrf
                                <button type="submit" class="flex items-center gap-1 px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 shadow-md transition transform hover:scale-105">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    MUSNAHKAN
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            Tidak ada berkas yang menunggu pemusnahan. <br>
                            (Pindahkan data dari menu <strong>Retensi RM</strong> terlebih dahulu)
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- RIWAYAT SUDAH MUSNAH -->
    <div class="mt-8">
        <h4 class="font-bold text-gray-500 mb-4 text-sm uppercase tracking-wider">Riwayat Pemusnahan (10 Terakhir)</h4>
        <div class="bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
            <table class="w-full text-sm text-gray-500">
                <thead class="bg-gray-200 text-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left">No RM</th>
                        <th class="px-6 py-3 text-left">Nama Pasien</th>
                        <th class="px-6 py-3 text-left">Status Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($destroyed as $d)
                    <tr>
                        <td class="px-6 py-3 font-mono">{{ $d->no_rm }}</td>
                        <td class="px-6 py-3">{{ $d->nama_pasien }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 bg-gray-600 text-white rounded text-[10px] uppercase">Dimusnahkan</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection