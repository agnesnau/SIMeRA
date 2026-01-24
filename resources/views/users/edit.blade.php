@extends('layouts.app')
@section('title', 'Edit Data User')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
    
    <div class="mb-6 border-b border-gray-100 pb-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Form Edit User</h2>
        <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <!-- Tampilkan Error Validasi -->
    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-600 p-4 rounded-lg text-sm border-l-4 border-red-500">
            <strong class="block mb-1">Periksa kembali inputan:</strong>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Update (Perhatikan method PUT) -->
    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') 
        
        <!-- 1. Input NIP -->
        <div class="mb-5">
            <label for="nip" class="block mb-2 text-sm font-medium text-gray-700">NIP</label>
            <input type="text" name="nip" id="nip" value="{{ old('nip', $user->nip) }}" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
        </div>

        <!-- 2. Input Nama Lengkap -->
        <div class="mb-5">
            <label for="nama_lengkap" class="block mb-2 text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
        </div>

        <!-- 3. Input Username -->
        <div class="mb-5">
            <label for="username" class="block mb-2 text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
        </div>

        <!-- 4. Input Password (Opsional) -->
        <div class="mb-5">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password Baru <span class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah)</span></label>
            <input type="password" name="password" id="password" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                placeholder="••••••">
        </div>

        <!-- 5. Input Level -->
        <div class="mb-6">
            <label for="level" class="block mb-2 text-sm font-medium text-gray-700">Level Akses</label>
            <select name="level" id="level" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white">
                <option value="admin" {{ old('level', $user->level) == 'admin' ? 'selected' : '' }}>Admin (Akses Penuh)</option>
                <option value="petugas" {{ old('level', $user->level) == 'petugas' ? 'selected' : '' }}>Petugas (Rekam Medis)</option>
                <option value="kepala_puskesmas" {{ old('level', $user->level) == 'kepala_puskesmas' ? 'selected' : '' }}>Kepala Puskesmas</option>
            </select>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Foto Profil Baru</label>
        
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-2xl bg-emerald-50 border-2 border-dashed border-emerald-200 flex items-center justify-center overflow-hidden">
                @if($user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-8 h-8 text-emerald-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                @endif
            </div>

            <div class="flex-1">
                <input type="file" name="foto" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer outline-none">
                <p class="mt-2 text-[10px] text-gray-400 italic">* Format: JPG, PNG. Maksimal 2MB.</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold uppercase">Batal</a>
        <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition">Simpan Perubahan</button>
    </div>
</form>
</div>
@endsection