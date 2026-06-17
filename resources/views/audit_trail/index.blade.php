@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-50 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <h3 class="font-extrabold text-gray-800 text-sm">Semua Riwayat Aktivitas (Audit Trail)</h3>
        </div>
        <span class="text-[10px] text-gray-400 italic">Total: {{ $logs->total() }} Aktivitas</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Aksi</th>
                    <th class="px-6 py-4">Detail</th>
                </tr>
            </thead>
            <tbody id="activity-log-body" class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors animate-fade-in">
                    <td class="px-6 py-3 whitespace-nowrap text-gray-500 font-mono text-xs">
                        {{ $log->created_at->format('d M Y H:i:s') }}
                    </td>
                    <td class="px-6 py-3 font-medium text-gray-800 flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 font-bold">
                            {{ substr($log->user->nama_lengkap ?? '?', 0, 1) }}
                        </div>
                        {{ $log->user->nama_lengkap ?? 'System' }}
                    </td>
                    <td class="px-6 py-3">
                        @if($log->action_type == 'verifikasi_fisik')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-semibold">Verifikasi Fisik</span>
                        @elseif($log->action_type == 'ajukan_musnah')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md text-xs font-semibold">Ajukan Musnah</span>
                        @elseif($log->action_type == 'eksekusi_musnah')
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-semibold">Pemusnahan</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-xs">{{ $log->action_type }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-gray-600">
                        RM: <span class="font-bold">{{ $log->patient->no_rm ?? '-' }}</span> - {{ $log->keterangan }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                        Belum ada aktivitas retensi/pemusnahan tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Area Pagination (Tombol Next/Prev Tailwind Laravel) -->
    <div class="px-6 py-4 border-t border-gray-50">
        {{ $logs->links() }}
    </div>
</div>
@endsection