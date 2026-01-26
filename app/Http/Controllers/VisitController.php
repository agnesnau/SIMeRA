<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    /**
     * Tampilkan daftar riwayat kunjungan dengan pencarian dan filter waktu.
     */
    public function index(Request $request)
    {
        // Gunakan Eager Loading untuk efisiensi query
        $query = Visit::with('patient');

        // Perbaikan Sintaks Pencarian (Gunakan penggabungan string yang benar)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                  ->orWhere('no_rm', 'like', '%' . $search . '%');
            });
        }

        // Filter Waktu Kunjungan
        if ($request->filled('filter_time')) {
            $now = Carbon::now();
            switch ($request->filter_time) {
                case 'minggu': 
                    $query->where('tgl_kunjungan', '>=', $now->startOfWeek()); 
                    break;
                case '3_bulan': 
                    $query->where('tgl_kunjungan', '>=', $now->subMonths(3)); 
                    break;
                case '6_bulan': 
                    $query->where('tgl_kunjungan', '>=', $now->subMonths(6)); 
                    break;
                case '1_tahun': 
                    $query->where('tgl_kunjungan', '>=', $now->subYears(1)); 
                    break;
                case '2_tahun': 
                    $query->where('tgl_kunjungan', '>=', $now->subYears(2)); 
                    break;
                case 'lebih_2_tahun': 
                    $query->where('tgl_kunjungan', '<', $now->subYears(2)); 
                    break;
            }
        }

        $sort = $request->get('sort_order', 'desc');
        $visits = $query->orderBy('tgl_kunjungan', $sort)->paginate(10);

        return view('visits.index', compact('visits'));
    }

    /**
     * Tampilkan form pembuatan kunjungan baru.
     */
    public function create()
    {
        $patients = Patient::orderBy('nama_pasien')->get();
        return view('visits.create', compact('patients'));
    }

    /**
     * Simpan data kunjungan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'    => 'required|exists:patients,id',
            'tgl_kunjungan' => 'required|date',
            'poli_tujuan'   => 'required|string',
            'dokter'        => 'required|string',
            'diagnosa'      => 'required|string',
        ]);

        // Generate No Registrasi Unik
        $no_reg = 'REG-' . date('Ymd') . '-' . rand(100, 999);

        Visit::create([
            'no_registrasi' => $no_reg,
            'patient_id'    => $request->patient_id,
            'tgl_kunjungan' => $request->tgl_kunjungan,
            'poli_tujuan'   => $request->poli_tujuan,
            'dokter'        => $request->dokter,
            'diagnosa'      => $request->diagnosa,
            'user_id'       => Auth::id(),
        ]);

        return redirect()->route('visits.index')->with('success', 'Kunjungan berhasil dicatat!');
    }

    /**
     * Menampilkan Detail Kunjungan (Lembar Medis Klinis Selayar Penuh)
     */
    public function show($id)
    {
        $visit = Visit::with(['patient', 'user'])->findOrFail($id);
        return view('visits.show', compact('visit'));
    }

    /**
     * Form edit riwayat kunjungan.
     */
    public function edit($id)
    {
        $visit = Visit::findOrFail($id);
        $patients = Patient::orderBy('nama_pasien')->get();
        return view('visits.edit', compact('visit', 'patients'));
    }

    /**
     * Update riwayat kunjungan.
     */
    public function update(Request $request, $id)
    {
        $visit = Visit::findOrFail($id);
        
        $request->validate([
            'tgl_kunjungan' => 'required|date',
            'poli_tujuan'   => 'required|string',
            'dokter'        => 'required|string',
            'diagnosa'      => 'required|string',
        ]);

        $visit->update([
            'tgl_kunjungan' => $request->tgl_kunjungan,
            'poli_tujuan'   => $request->poli_tujuan,
            'dokter'        => $request->dokter,
            'diagnosa'      => $request->diagnosa,
        ]);

        return redirect()->route('visits.index')->with('success', 'Riwayat kunjungan diperbarui!');
    }

    /**
     * Hapus riwayat kunjungan.
     */
    public function destroy($id)
    {
        Visit::findOrFail($id)->delete();
        return redirect()->route('visits.index')->with('success', 'Data riwayat kunjungan telah dihapus.');
    }
}