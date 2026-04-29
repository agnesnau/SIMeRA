@extends('layouts.app')
@section('title', 'Registrasi Pasien Baru')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ openVisit: false }">
    <form action="{{ route('patients.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white p-10 rounded-3xl shadow-sm border border-gray-100">
            <div class="mb-8 pb-6 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Biodata Pasien</h2>
                    <p class="text-sm text-gray-400 font-medium">Lengkapi identitas dasar pasien baru.</p>
                </div>
                <a href="{{ route('patients.index') }}" class="px-5 py-2 bg-gray-50 text-gray-400 rounded-xl text-xs font-bold border border-gray-200 uppercase tracking-widest hover:bg-gray-100 transition">Batal</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-5">
                    <div>
                        <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Index (No. RM)</label>
                        <input type="text" name="no_rm" value="{{ old('no_rm') }}" required 
                            class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none font-mono font-bold text-lg tracking-tighter"
                            placeholder="143509...">
                    </div>
                    <div>
                        <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">NIK (Identitas)</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" 
                            class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono font-bold"
                            placeholder="3509...">
                    </div>
                    <div>
                        <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Lengkap Pasien</label>
                        <input type="text" name="nama_pasien" value="{{ old('nama_pasien') }}" required 
                            class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none font-black text-slate-800 uppercase">
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tgl Lahir</label>
                            <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none font-bold">
                        </div>
                        <div>
                            <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kelamin</label>
                            <select name="jenis_kelamin" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none font-bold bg-white cursor-pointer">
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>L</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>P</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat Domisili</label>
                        <textarea name="alamat_lengkap" rows="4" 
                            class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none text-slate-600 italic">{{ old('alamat_lengkap') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-600 rounded-3xl shadow-xl overflow-hidden transition-all">
            <div class="p-6 flex items-center justify-between cursor-pointer select-none" @click="openVisit = !openVisit">
                <div class="flex items-center gap-4 text-white">
                    <div class="p-2 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-black uppercase text-sm tracking-widest">Catat Kunjungan Pertama?</h3>
                        <p class="text-[10px] text-blue-100 font-medium italic">Klik untuk membuka formulir detail kunjungan (Poli & Pembayaran)</p>
                    </div>
                </div>
                <input type="checkbox" name="catat_kunjungan" x-model="openVisit" class="hidden">
                <div class="w-12 h-6 rounded-full relative transition-colors border-2 border-white/30" :class="openVisit ? 'bg-emerald-500' : 'bg-blue-800'">
                    <div class="absolute top-0.5 w-4 h-4 bg-white rounded-full transition-all" :class="openVisit ? 'left-6' : 'left-1'"></div>
                </div>
            </div>

            <div x-show="openVisit" x-cloak x-transition.scale.origin.top class="p-10 bg-white border-t border-blue-50 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div>
                            <label class="block mb-2 text-[10px] font-black text-blue-400 uppercase tracking-widest">Tanggal Kunjungan</label>
                            <input type="date" name="tgl_kunjungan" value="{{ date('Y-m-d') }}" 
                                class="w-full px-5 py-3 bg-blue-50 border border-blue-100 rounded-2xl outline-none font-bold text-blue-900 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block mb-2 text-[10px] font-black text-blue-400 uppercase tracking-widest">Poli / Unit Tujuan</label>
                            <select name="poli_tujuan" class="w-full px-5 py-3 bg-blue-50 border border-blue-100 rounded-2xl font-bold text-blue-900 bg-white cursor-pointer">
                                <option value="Umum">POLI UMUM</option>
                                <option value="Gigi">POLI GIGI</option>
                                <option value="KIA">KIA / KB</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label class="block mb-2 text-[10px] font-black text-blue-400 uppercase tracking-widest">Metode Pembayaran</label>
                            <select name="pembayaran" class="w-full px-5 py-3 bg-blue-50 border border-blue-100 rounded-2xl font-bold text-blue-900 bg-white cursor-pointer">
                                <option value="BPJS">BPJS KESEHATAN</option>
                                <option value="UMUM">UMUM (MANDIRI)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6">
            <button type="submit" class="w-full md:w-auto px-12 py-4 bg-emerald-600 text-white rounded-2xl font-black shadow-xl shadow-emerald-100 hover:bg-emerald-700 transition-all uppercase tracking-widest text-xs active:scale-95">
                Simpan Rekam Medis
            </button>
        </div>
    </form>
</div>
@endsection