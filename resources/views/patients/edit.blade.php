@extends('layouts.app')
@section('title', 'Edit Data Pasien')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-gray-100">
    
    <div class="mb-8 pb-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Perbarui Informasi Pasien</h2>
            <p class="text-sm text-gray-400 font-medium">Melakukan pemutakhiran biodata dasar pasien.</p>
        </div>
        <a href="{{ route('patients.index') }}" class="px-5 py-2.5 bg-gray-50 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-100 transition border border-gray-200 uppercase tracking-widest flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-8 bg-red-50 text-red-600 p-5 rounded-2xl text-xs border-l-4 border-red-500 font-bold uppercase tracking-widest animate-pulse">
            @foreach ($errors->all() as $error) <span>● {{ $error }}</span><br> @endforeach
        </div>
    @endif

    <form action="{{ route('patients.update', $patient->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            
            <div class="space-y-6">
                
                <div>
                    <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Rekam Medis / Index</label>
                    <input type="text" name="no_rm" value="{{ old('no_rm', $patient->no_rm) }}" required 
                        class="w-full px-5 py-3.5 bg-slate-100 border border-slate-200 rounded-2xl font-mono font-black text-lg tracking-tighter text-slate-500 cursor-not-allowed" readonly>
                    <p class="text-[9px] text-red-400 mt-2 font-bold italic uppercase tracking-tighter">* NOMOR INDEX BERSIFAT PERMANEN & TIDAK DAPAT DIUBAH.</p>
                </div>

                <div>
                    <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">NIK Pasien</label>
                    <input type="text" name="nik" value="{{ old('nik', $patient->nik) }}" 
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none font-bold text-slate-700">
                </div>

                <div>
                    <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</label>
                    <input type="text" name="nama_pasien" value="{{ old('nama_pasien', $patient->nama_pasien) }}" required 
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none font-black text-slate-800 uppercase">
                </div>

            </div>

            <div class="space-y-6">
                
                <div>
                    <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Lahir</label>
                    
                    {{-- FIX DATE FORMAT: Mengubah format database ke Y-m-d agar terbaca input date --}}
                    <input type="date" name="tgl_lahir" 
                        value="{{ old('tgl_lahir', \Carbon\Carbon::parse($patient->tgl_lahir)->format('Y-m-d')) }}" 
                        required 
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none font-bold text-slate-700">
                </div>

                <div>
                    <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis Kelamin</label>
                    <div class="relative">
                        <select name="jenis_kelamin" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none font-bold bg-white cursor-pointer uppercase appearance-none text-slate-700">
                            <option value="L" {{ old('jenis_kelamin', $patient->jenis_kelamin) == 'L' ? 'selected' : '' }}>LAKI-LAKI</option>
                            <option value="P" {{ old('jenis_kelamin', $patient->jenis_kelamin) == 'P' ? 'selected' : '' }}>PEREMPUAN</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" rows="5" 
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none font-medium text-slate-600 leading-relaxed italic resize-none">{{ old('alamat_lengkap', $patient->alamat_lengkap) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-12 pt-8 border-t border-gray-100">
            <button type="submit" class="w-full md:w-auto px-12 py-4 bg-emerald-600 text-white rounded-2xl font-black shadow-xl shadow-emerald-100 hover:bg-emerald-700 transition-all uppercase tracking-widest text-xs active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection