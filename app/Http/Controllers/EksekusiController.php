<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\RetentionAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EksekusiController extends Controller
{
    /**
     * Menampilkan daftar berkas yang SIAP DIMUSNAHKAN
     * (Biasanya status manualnya 'siap_musnah' atau hasil filter tahun > 5 thn)
     */
    public function index(Request $request)
    {
        // Asumsi: Data yang masuk sini adalah yang statusnya 'siap_musnah'
        // atau yang sudah disetujui lewat Retensi
        $query = Patient::with(['lastVisit'])
                        ->where('manual_status', 'siap_musnah'); // Sesuaikan status ini dengan flow Anda sebelumnya

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(10);
        return view('pemusnahan.eksekusi', compact('patients'));
    }

    /**
     * FUNGSI BARU: MUSNAHKAN SEMUA (MASSAL)
     */
    public function destroyAll()
    {
        // 1. Ambil semua pasien yang statusnya 'siap_musnah'
        $patients = Patient::where('manual_status', 'siap_musnah')->get();

        if ($patients->isEmpty()) {
            return back()->with('error', 'Tidak ada berkas yang siap dieksekusi.');
        }

        DB::transaction(function () use ($patients) {
            // A. Update semua jadi 'dimusnahkan'
            // Ini akan otomatis membuat mereka hilang dari list ini & Cetak BA (karena status berubah)
            // Dan akan muncul di Master Data dengan label 'Musnah'
            Patient::where('manual_status', 'siap_musnah')
                   ->update(['manual_status' => 'dimusnahkan']);

            // B. Catat Log
            foreach ($patients as $p) {
                RetentionAction::create([
                    'patient_id'  => $p->id,
                    'user_id'     => auth()->id(),
                    'action_type' => 'pemusnahan_fisik',
                    'keterangan'  => 'Berkas fisik telah dimusnahkan (dicacah/bakar) sesuai SOP.'
                ]);
            }
        });

        return back()->with('success', 'Eksekusi Selesai! Seluruh berkas telah ditandai sebagai DIMUSNAHKAN.');
    }
}