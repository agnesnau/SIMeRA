<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\RetentionAction;
use Illuminate\Http\Request;

class PemilahanController extends Controller
{
    /**
     * Menampilkan daftar pasien yang sedang dalam proses pemilahan.
     */
    public function index(Request $request)
    {
        // Hanya ambil pasien dengan status manual 'pemilahan'
        $query = Patient::with(['lastVisit', 'actions'])
                        ->where('manual_status', 'pemilahan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(10);

        return view('retention.pemilahan', compact('patients'));
    }

    /**
     * Menyelesaikan proses pemilahan dan memindahkan ke Inaktif permanen.
     */
    public function finishSorting($id)
    {
        $patient = Patient::findOrFail($id);
        
        // Kembalikan ke NULL agar sistem menghitung ulang secara otomatis (Status jadi Inaktif)
        $patient->update(['manual_status' => null]);

        RetentionAction::create([
            'patient_id'  => $id,
            'user_id'     => auth()->id(),
            'action_type' => 'verifikasi_fisik',
            'keterangan'  => 'Proses Pemilahan Berkas Selesai. Berkas dipindahkan ke Gudang Inaktif.'
        ]);

        return redirect()->route('retensi.pemilahan')->with('success', 'Pemilahan selesai. Berkas resmi masuk Ruang Inaktif.');
    }
}