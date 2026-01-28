@extends('layouts.app')

@section('title', 'Edit Data Pengguna')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Pengguna</h1>
            <p class="text-sm text-gray-500">Perbarui informasi akun pengguna sistem.</p>
        </div>
        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-bold">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">NIP / ID Pegawai</label>
                        <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Contoh: 1985xxxx...">
                        @error('nip') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Level Akses</label>
                        
                        {{-- Hanya Admin yang bisa ganti Level --}}
                        @if(auth()->user()->level === 'admin')
                            <select name="level" class="w-full px-4 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer">
                                <option value="admin" {{ $user->level == 'admin' ? 'selected' : '' }}>Administrator Sistem</option>
                                <option value="petugas" {{ $user->level == 'petugas' ? 'selected' : '' }}>Petugas Rekam Medis</option>
                                
                                {{-- PASTIKAN VALUE NYA 'supervisor' BIAR SINKRON SAMA CONTROLLER --}}
                                <option value="supervisor" {{ $user->level == 'supervisor' ? 'selected' : '' }}>Kepala Puskesmas</option>
                            </select>
                        @else
                            {{-- Kalau bukan admin, cuma bisa lihat levelnya aja (gak bisa ubah) --}}
                            <input type="hidden" name="level" value="{{ $user->level }}">
                            <input type="text" value="{{ ucfirst($user->level == 'supervisor' ? 'Kepala Puskesmas' : $user->level) }}" class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                        @endif
                    </div>
                </div>

                <hr class="border-gray-100">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Password Baru <span class="text-gray-400 font-normal text-xs">(Opsional)</span></label>
                        <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Isi jika ingin mengganti password">
                        <p class="text-[10px] text-gray-400 mt-1">Minimal 6 karakter. Kosongkan jika tidak ingin diubah.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Profil</label>
                        <div class="flex items-center gap-4">
                            @if($user->foto)
                                <img src="{{ asset('storage/' . $user->foto) }}" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                    {{ substr($user->nama_lengkap, 0, 1) }}
                                </div>
                            @endif
                            <input type="file" name="foto" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-4">
                    <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-bold text-sm">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 font-bold text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection