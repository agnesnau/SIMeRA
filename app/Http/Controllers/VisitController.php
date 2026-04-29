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
        $query = Visit::with('patient');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                  ->orWhere('no_rm', 'like', '%' . $search . '%');
            });
        }

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
        // 1. Validasi disesuaikan dengan Form HTML (Tanpa Dokter/Diagnosa, Ada Pembayaran)
        $request->validate([
            'patient_id'    => 'required|exists:patients,id',
            'tgl_kunjungan' => 'required|date',
            'poli_tujuan'   => 'required|string',
            'pembayaran'    => 'required|string',
        ]);

        $no_reg = 'REG-' . date('Ymd') . '-' . rand(100, 999);

        // 2. Simpan Kunjungan Baru
        Visit::create([
            'no_registrasi' => $no_reg,
            'patient_id'    => $request->patient_id,
            'tgl_kunjungan' => $request->tgl_kunjungan,
            'poli_tujuan'   => $request->poli_tujuan,
            'pembayaran'    => $request->pembayaran,
            'user_id'       => Auth::id(),
        ]);

        // ---------------------------------------------------------
        // 3. LOGIKA RE-AKTIVASI (Meriset status gudang menjadi Aktif)
        // ---------------------------------------------------------
        $patient = Patient::find($request->patient_id);
        
        $status_sebelumnya = $patient->manual_status;

        $patient->update([
            'manual_status'   => '',
            'status_approval' => 0
        ]);

        if (in_array($status_sebelumnya, ['digudang', 'pemilahan', 'siap_musnah'])) {
            return redirect()->route('visits.index')->with('success', 'Kunjungan dicatat. Berkas Rekam Medis pasien ini ditarik dari Gudang dan berstatus AKTIF kembali.');
        }

        return redirect()->route('visits.index')->with('success', 'Kunjungan berhasil dicatat!');
    }

    /**
     * Menampilkan Detail Kunjungan
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
        
        // Sesuaikan validasi update dengan store (Tanpa dokter/diagnosa)
        $request->validate([
            'tgl_kunjungan' => 'required|date',
            'poli_tujuan'   => 'required|string',
            'pembayaran'    => 'required|string',
        ]);

        $visit->update([
            'tgl_kunjungan' => $request->tgl_kunjungan,
            'poli_tujuan'   => $request->poli_tujuan,
            'pembayaran'    => $request->pembayaran,
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