@extends('layouts.app')
@section('title', 'Data Kunjungan Pasien')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <div>
            <h3 class="font-bold text-gray-700">Riwayat Kunjungan</h3>
            <p class="text-xs text-gray-500">Semua poli dan unit pelayanan</p>
        </div>
        <a href="{{ route('visits.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 transition-colors flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Catat Kunjungan
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-white text-gray-600 font-bold border-b">
                <tr>
                    <th class="px-6 py-4">Tgl Kunjungan</th>
                    <th class="px-6 py-4">No Registrasi</th>
                    <th class="px-6 py-4">Nama Pasien / RM</th>
                    <th class="px-6 py-4">Poli Tujuan</th>
                    <th class="px-6 py-4">Diagnosa</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($visits as $visit)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-600">
                        {{ $visit->tgl_kunjungan ? $visit->tgl_kunjungan->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 font-mono text-emerald-600 text-xs font-bold">
                        {{ $visit->no_registrasi }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $visit->patient->nama_pasien ?? 'Pasien Dihapus' }}</div>
                        <div class="text-xs text-gray-400 font-mono">{{ $visit->patient->no_rm ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs border border-blue-100">
                            {{ $visit->poli_tujuan }}
                        </span>
                        @if($visit->dokter)
                        <div class="text-xs text-gray-400 mt-1">dr. {{ $visit->dokter }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600 truncate max-w-xs" title="{{ $visit->diagnosa }}">
                        {{ \Illuminate\Support\Str::limit($visit->diagnosa, 30) }}
                    </td>
                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                        <!-- TOMBOL VIEW (BARU) -->
                        <a href="{{ route('visits.show', $visit->id) }}" class="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Lihat Detail">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>

                        <form action="{{ route('visits.destroy', $visit->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat kunjungan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        Belum ada riwayat kunjungan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-100">
        {{ $visits->links() }}
    </div>
</div>
@endsection