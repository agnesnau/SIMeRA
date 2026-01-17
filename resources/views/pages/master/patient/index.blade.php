@extends('layouts.app')
@section('title', 'Data Master Pasien')

@section('content')
<div class="bg-white rounded shadow overflow-hidden">
    <div class="p-4 border-b">
        <button class="bg-emerald-600 text-white px-3 py-2 rounded text-sm">Tambah Pasien</button>
    </div>
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4">No RM</th>
                <th class="p-4">Nama</th>
                <th class="p-4">Tgl Lahir</th>
                <th class="p-4">Alamat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $p)
            <tr class="border-b">
                <td class="p-4 font-bold text-emerald-600">{{ $p->no_rm }}</td>
                <td class="p-4">{{ $p->nama_pasien }}</td>
                <td class="p-4">{{ $p->tgl_lahir }}</td>
                <td class="p-4 truncate max-w-xs">{{ $p->alamat_lengkap }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
