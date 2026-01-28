<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\RetentionAction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DestructionController extends Controller
{
    /**
     * 1. TAMPILKAN DAFTAR UTAMA PEMUSNAHAN (OTOMATIS > 4 TAHUN)
     */
    public function index(Request $request)
    {
        // --- BAGIAN A: HITUNG STATISTIK (SAMA KAYA SEBELUMNYA) ---
        $allPatients = Patient::with('lastVisit')->get();
        $stats = ['total_aktif'=>0, 'total_inaktif'=>0, 'siap_musnah'=>0, 'total_pemilahan'=>0];
        foreach ($allPatients as $p) {
            $last = $p->lastVisit ? $p->lastVisit->tgl_kunjungan : null;
            $y = $last ? \Carbon\Carbon::parse($last)->diffInYears(now()) : 0;
            if($p->manual_status==='dimusnahkan') continue;
            if($y<2) $stats['total_aktif']++; elseif($y<4) $stats['total_inaktif']++; else $stats['siap_musnah']++;
        }

        // --- BAGIAN B: QUERY DATA (FIX SQL STRICT MODE) ---
        $fourYearsAgo = \Carbon\Carbon::now()->subYears(4);
        
        // Default Sortir
        $sortDir = $request->input('sort') == 'asc' ? 'asc' : 'desc';

        $query = Patient::select('patients.*') // Ambil data pasien
            ->join('visits', 'patients.id', '=', 'visits.patient_id')
            ->where('visits.tgl_kunjungan', '<', $fourYearsAgo) // Filter Umur > 4 Tahun
            ->where(function($q) {
                // Filter Status (Bukan yg udah musnah/abadi)
                $q->whereNull('manual_status')
                  ->orWhereNotIn('manual_status', ['siap_musnah', 'dimusnahkan', 'abadi']);
            })
            ->groupBy('patients.id') // SOLUSI ERROR 3065: Kelompokkan per pasien
            ->orderByRaw("MAX(visits.tgl_kunjungan) $sortDir"); // Urutkan berdasarkan tanggal kunjungan terakhir di grup itu

        // 1. Pencarian
        if ($request->filled('search')) {
            $query->where('nama_pasien', 'like', "%{$request->search}%");
        }

        $candidates = $query->paginate(10);

        return view('pemusnahan.index', compact('candidates', 'stats'));
    }
    /**
     * 2. PROSES PENILAIAN SATUAN (MODAL POPUP) - SUDAH DIPERBAIKI
     * Logic: Mau ada nilai atau tidak, SEMUA MASUK EKSEKUSI.
     */
    public function storeAssessment(Request $request, $id)
    {
        $request->validate(['nilai_guna' => 'required']);
        $patient = Patient::findOrFail($id);

        // SKENARIO A: TIDAK ADA NILAI GUNA
        if ($request->nilai_guna === 'none') {
            $patient->update(['manual_status' => 'siap_musnah']);
            $this->logAction($id, 'penilaian_alfred', 'Tidak ada nilai guna. Masuk antrian eksekusi.');
            
            // Redirect langsung ke Halaman Eksekusi
            return redirect()->route('pemusnahan.eksekusi')
                ->with('success', 'Verifikasi Selesai. Berkas masuk antrian Eksekusi.');
        } 
        
        // SKENARIO B: ADA NILAI GUNA (Upload + Tetap Pindah Eksekusi)
        elseif ($request->nilai_guna === 'bernilai') {
            
            $request->validate([
                'file_nilai_guna' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ]);

            $filePath = $request->file('file_nilai_guna')->store('arsip_bernilai', 'public');

            // UPDATE STATUS TETAP KE 'siap_musnah' (Biar nongol di Eksekusi)
            $patient->update([
                'manual_status'  => 'siap_musnah', 
                'alamat_lengkap' => $patient->alamat_lengkap . " [ARSIP DIGITAL AMAN]" 
            ]);

            $this->logAction($id, 'penyelamatan_arsip', "Arsip Digital Disimpan. Fisik dikirim ke Eksekusi.");

            // Redirect langsung ke Halaman Eksekusi
            return redirect()->route('pemusnahan.eksekusi')
                ->with('success', 'Arsip Digital Aman! Silakan proses fisik berkas di Halaman Eksekusi.');
        }
    }

    /**
     * 3. PROSES PENILAIAN MASSAL (CHECKBOX)
     */
    public function bulkAction(Request $request)
    {
        if (!$request->filled('ids') || !$request->filled('action_type')) {
            return back()->with('error', 'Pilih data dan tindakan terlebih dahulu.');
        }

        $ids = explode(',', $request->ids);
        $jumlah = count($ids);
        $actionType = $request->action_type;

        foreach($ids as $id) {
            $patient = Patient::find($id);
            if(!$patient) continue;

            if ($actionType === 'siap_musnah') {
                $patient->update(['manual_status' => 'siap_musnah']);
                $this->logAction($id, 'penilaian_massal', 'Penilaian Massal: Ditandai SIAP MUSNAH.');
            } 
            elseif ($actionType === 'abadi') {
                // Bulk action khusus untuk simpan abadi (kalau ada)
                $patient->update(['manual_status' => 'abadi']);
                $this->logAction($id, 'penyelamatan_massal', 'Penilaian Massal: Disimpan sebagai ARSIP ABADI.');
            }
        }

        return back()->with('success', "Berhasil memproses $jumlah berkas secara massal.");
    }

    // Helper Log
    private function logAction($patientId, $type, $ket)
    {
        RetentionAction::create([
            'patient_id'  => $patientId,
            'user_id'     => auth()->id(),
            'action_type' => $type,
            'keterangan'  => $ket
        ]);
    }
}