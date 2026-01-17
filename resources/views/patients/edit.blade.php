@extends('layouts.app')
@section('title', 'Edit Data Pasien')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
    
    <div class="mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Edit Pasien: {{ $patient->nama_pasien }}</h2>
        <a href="{{ route('patients.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-lg text-sm border-l-4 border-red-500">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('patients.update', $patient->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kolom Kiri -->
            <div class="space-y-5">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Nomor Rekam Medis</label>
                    <input type="text" name="no_rm" value="{{ old('no_rm', $patient->no_rm) }}" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-gray-50" readonly>
                    <p class="text-xs text-gray-400 mt-1">No RM tidak dapat diubah sembarangan.</p>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">NIK</label>
                    <input type="number" name="nik" value="{{ old('nik', $patient->nik) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="nama_pasien" value="{{ old('nama_pasien', $patient->nama_pasien) }}" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                <!-- FIELD BARU: EDIT KUNJUNGAN TERAKHIR -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <label class="block mb-2 text-sm font-bold text-blue-800">Tanggal Kunjungan Terakhir</label>
                    <input type="date" name="tgl_kunjungan_terakhir" 
                        value="{{ old('tgl_kunjungan_terakhir', optional($patient->lastVisit)->tgl_kunjungan ? $patient->lastVisit->tgl_kunjungan->format('Y-m-d') : '') }}" 
                        class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none text-blue-900 font-medium">
                    <p class="text-xs text-blue-600 mt-1">
                        Mengubah tanggal ini akan mempengaruhi <strong>Status Retensi</strong> (Aktif/Inaktif).
                    </p>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $patient->tempat_lahir) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir', $patient->tgl_lahir) }}" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <select name="jenis_kelamin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white">
                        <option value="L" {{ old('jenis_kelamin', $patient->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $patient->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Nomor BPJS</label>
                    <input type="number" name="no_bpjs" value="{{ old('no_bpjs', $patient->no_bpjs) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $patient->no_hp) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" rows="3" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none">{{ old('alamat_lengkap', $patient->alamat_lengkap) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
            <a href="{{ route('patients.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium shadow-lg shadow-emerald-200">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection