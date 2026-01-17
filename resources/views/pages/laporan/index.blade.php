@extends('layouts.app')
@section('title', 'Pelaporan & Berita Acara')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
    </div>
    <h3 class="text-xl font-bold text-gray-800">Menu Pelaporan</h3>
    <p class="text-gray-500 mt-2">Silakan pilih jenis laporan yang ingin dicetak.</p>
    
    <div class="mt-8 flex justify-center gap-4">
        <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank">
            @csrf
            <button class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition">
                Cetak Berita Acara Pemusnahan
            </button>
        </form>
    </div>
</div>
@endsection