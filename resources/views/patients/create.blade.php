@extends('layouts.app')

@section('title', 'Tambah Pasien Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pendaftaran Pasien Baru</h2>
            <p class="text-gray-600 text-sm">Masukkan data identitas dan domisili pasien.</p>
        </div>
        <a href="{{ route('patients.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm shadow">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
        
        <form action="{{ route('patients.store') }}" method="POST">
            @csrf

            <div class="mb-6 border-b pb-4">
                <h3 class="text-lg font-semibold text-blue-600 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Identitas Pribadi
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">No. Rekam Medis</label>
                        <input type="text" name="no_rm" value="{{ old('no_rm') }}" placeholder="Contoh: 00-12-34"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('no_rm') border-red-500 @enderror">
                        @error('no_rm') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">NIK (16 Digit)</label>
                        <input type="number" name="nik" value="{{ old('nik') }}" placeholder="3509xxxxxxxxxxxx"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nik') border-red-500 @enderror">
                        @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_pasien" value="{{ old('nama_pasien') }}" placeholder="Nama sesuai KTP"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_pasien') border-red-500 @enderror">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-green-600 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Alamat Domisili
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Jalan / Dukuh</label>
                        <textarea name="alamat_jalan" rows="2" placeholder="Jl. Mastrip No. 12 / Dsn. Krajan"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('alamat_jalan') }}</textarea>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">RT</label>
                            <input type="text" name="rt" value="{{ old('rt') }}" placeholder="001"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">RW</label>
                            <input type="text" name="rw" value="{{ old('rw') }}" placeholder="002"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" value="{{ $kecamatan ?? 'Sumbersari' }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 bg-gray-200 text-gray-600 rounded-lg cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">*Wilayah kerja Puskesmas</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Desa / Kelurahan</label>
                        <select name="desa_kelurahan" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                            <option value="">-- Pilih Desa --</option>
                            
                            @if(isset($daftar_desa))
                                @foreach($daftar_desa as $desa)
                                    <option value="{{ $desa }}" {{ old('desa_kelurahan') == $desa ? 'selected' : '' }}>
                                        {{ $desa }}
                                    </option>
                                @endforeach
                            @else
                                <option value="Lainnya">Lainnya</option>
                            @endif

                        </select>
                        @error('desa_kelurahan') <p class="text-red-500 text-xs mt-1">Wajib dipilih!</p> @enderror
                    </div>

                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-4 border-t">
                <button type="reset" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">
                    Reset
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold shadow-md transition transform hover:scale-105">
                    Simpan Data
                </button>
            </div>

        </form>
    </div>
</div>
@endsection