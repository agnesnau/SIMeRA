<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

class ReportController extends Controller
{
    // 1. TAMPILKAN MENU PELAPORAN
    public function index()
    {
        // Hitung jumlah data untuk info di dashboard laporan
        $siapMusnah = Patient::where('manual_status', 'siap_musnah')->count();
        $sudahMusnah = Patient::where('manual_status', 'dimusnahkan')->count();

        return view('laporan.index', compact('siapMusnah', 'sudahMusnah'));
    }

    // 2. TAMPILKAN HALAMAN CETAK (BERITA ACARA)
    public function printBeritaAcara(Request $request)
    {
        // Ambil input dari form
        $no_surat = $request->input('no_surat', '001/BA-MUSNAH/' . date('Y'));
        $lokasi   = $request->input('lokasi', 'Ruang Arsip');
        $tanggal  = $request->input('tanggal', date('Y-m-d'));
        $ketua    = $request->input('ketua', '.........................');
        $nip_ketua= $request->input('nip_ketua', '.........................');

        // Ambil data yang akan dimusnahkan (Status: Siap Musnah & Dimusnahkan)
        $data_berkas = Patient::with('lastVisit')
                        ->whereIn('manual_status', ['siap_musnah', 'dimusnahkan'])
                        ->latest()
                        ->get();

        return view('laporan.cetak', compact('no_surat', 'lokasi', 'tanggal', 'ketua', 'nip_ketua', 'data_berkas'));
    }
}