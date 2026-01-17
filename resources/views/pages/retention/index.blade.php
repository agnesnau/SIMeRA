@extends('layouts.app')
@section('title', 'Retensi Rekam Medis')

@section('content')
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4">No RM</th>
                <th class="p-4">Nama</th>
                <th class="p-4">Kunjungan Terakhir</th>
                <th class="p-4">Status</th>
                <th class="p-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $p)
            <tr class="border-b">
                <td class="p-4 font-bold">{{ $p->no_rm }}</td>
                <td class="p-4">{{ $p->nama_pasien }}</td>
                <td class="p-4">{{ $p->lastVisit?->tgl_kunjungan?->format('d-M-Y') ?? '-' }}</td>
                <td class="p-4">
                    <span class="px-2 py-1 rounded text-xs 
                        {{ $p->current_status == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $p->current_status }}
                    </span>
                </td>
                <td class="p-4 flex gap-2">
                    @if($p->current_status == 'Inaktif')
                        <form action="{{ route('retensi.verify', $p->id) }}" method="POST">
                            @csrf
                            <button class="text-blue-600 underline text-xs">Cek Fisik</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
