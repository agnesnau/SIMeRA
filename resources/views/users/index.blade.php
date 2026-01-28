@extends('layouts.app')
@section('title', 'Data User')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-700">Daftar Pengguna & Petugas</h3>
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 transition-colors flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah User
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 font-medium border-b bg-gray-50">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">NIP</th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Email / Username</th>
                    <th class="px-6 py-4">Level</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $index => $user)
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                    
                    <td class="px-6 py-4 font-mono text-gray-600">
                        {{ $user->nip ?? '-' }}
                    </td>

                    <td class="px-6 py-4 font-medium text-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs ring-2 ring-white shadow-sm">
                                {{ substr($user->nama_lengkap ?? $user->name, 0, 1) }}
                            </div>
                            {{ $user->nama_lengkap ?? $user->name }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->email ?? $user->username }}</td>
                    <td class="px-6 py-4">
                        @if($user->level == 'admin')
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200">
                                Admin
                            </span>
                        @elseif($user->level == 'supervisor')
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-200">
                                Kepala Puskesmas
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                Petugas
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                        <!-- TOMBOL VIEW (BARU) -->
                        <a href="{{ route('users.show', $user->id) }}" class="p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Lihat Detail">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>

                        <a href="{{ route('users.edit', $user->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->nama_lengkap }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 bg-gray-50/50">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p class="text-sm">Belum ada data user yang terdaftar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection