<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Patient;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    // 1. DAFTAR KUNJUNGAN
    public function index()
    {
        // Ambil data kunjungan beserta data pasien terkait (Eager Loading)
        $visits = Visit::with('patient')->latest('tgl_kunjungan')->paginate(10);
        return view('visits.index', compact('visits'));
    }

    // 2. FORM TAMBAH KUNJUNGAN
    public function create()
    {
        // Ambil semua pasien untuk dropdown (urutkan nama)
        $patients = Patient::orderBy('nama_pasien')->get();
        return view('visits.create', compact('patients'));
    }

    // 3. SIMPAN DATA
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'    => 'required|exists:patients,id',
            'tgl_kunjungan' => 'required|date',
            'poli_tujuan'   => 'required|string',
            'dokter'        => 'nullable|string',
            'diagnosa'      => 'required|string',
        ]);

        // Generate No Registrasi Otomatis (Contoh: REG-Timestamp)
        $no_reg = 'REG-' . time();

        Visit::create([
            'no_registrasi' => $no_reg,
            'patient_id'    => $request->patient_id,
            'tgl_kunjungan' => $request->tgl_kunjungan,
            'poli_tujuan'   => $request->poli_tujuan,
            'dokter'        => $request->dokter,
            'diagnosa'      => $request->diagnosa,
            'user_id'       => auth()->id(), // Petugas yang menginput
        ]);

        return redirect()->route('visits.index')->with('success', 'Kunjungan berhasil dicatat!');
    }

    // 4. HAPUS DATA
    public function destroy($id)
    {
        $visit = Visit::findOrFail($id);
        $visit->delete();
        return redirect()->route('visits.index')->with('success', 'Riwayat kunjungan dihapus.');
    }
    public function show($id)
    {
        // 1. Cari kunjungan berdasarkan ID
        $visit = \App\Models\Visit::findOrFail($id);

        // 2. Tampilkan ke halaman detail
        // Pastikan nanti kamu buat file view-nya di resources/views/visits/show.blade.php
        return view('visits.show', compact('visit'));
    }
}