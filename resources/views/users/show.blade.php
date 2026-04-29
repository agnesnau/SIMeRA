@extends('layouts.app')
@section('title', 'Detail Pengguna')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-emerald-50 p-8 text-center border-b border-emerald-100">
    @if($user->foto)
        <img src="{{ asset('storage/' . $user->foto) }}" 
             class="w-20 h-20 mx-auto rounded-full object-cover shadow-sm ring-4 ring-white mb-4" 
             alt="{{ $user->nama_lengkap }}">
    @else
        <div class="w-20 h-20 mx-auto bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-3xl font-bold mb-4 shadow-sm ring-4 ring-white">
            {{ substr($user->nama_lengkap, 0, 1) }}
        </div>
    @endif

    <h2 class="text-xl font-bold text-gray-800">{{ $user->nama_lengkap }}</h2>
    <p class="text-emerald-600 font-medium text-sm">{{ ucfirst($user->level) }}</p>
</div>
    <div class="p-6 space-y-4">
        <div class="flex justify-between border-b pb-2">
            <span class="text-gray-500">NIP</span>
            <span class="font-medium font-mono text-gray-800">{{ $user->nip ?? '-' }}</span>
        </div>
        <div class="flex justify-between border-b pb-2">
            <span class="text-gray-500">Email</span>
            <span class="font-medium text-gray-800">{{ $user->username }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Terdaftar Sejak</span>
            <span class="font-medium text-gray-800">{{ $user->created_at->format('d F Y') }}</span>
        </div>
    </div>

    <div class="p-4 bg-gray-50 text-center">
        <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-800 text-sm font-medium">Kembali ke Daftar</a>
    </div>
</div>
@endsection