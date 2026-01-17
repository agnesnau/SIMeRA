<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\RetentionAction;
use Illuminate\Http\Request;

class DestructionController extends Controller
{
    // 1. TAMPILKAN HALAMAN PEMUSNAHAN
    public function index()
    {
        // Ambil data yang statusnya 'siap_musnah' (Dari tombol Retensi)
        $readyToDestroy = Patient::with('lastVisit')
                            ->where('manual_status', 'siap_musnah')
                            ->latest()
                            ->get();

        // Ambil data yang SUDAH dimusnahkan (History)
        $destroyed = Patient::where('manual_status', 'dimusnahkan')
                            ->latest()
                            ->take(10)
                            ->get();

        return view('pemusnahan.index', compact('readyToDestroy', 'destroyed'));
    }

    // 2. RESTORE (Batal Musnah -> Kembali ke Inaktif)
    public function restore($id)
    {
        $patient = Patient::findOrFail($id);
        
        // Set null agar kembali mengikuti logika otomatis (2-4 tahun = inaktif)
        $patient->update(['manual_status' => null]); 

        RetentionAction::create([
            'patient_id'  => $id,
            'user_id'     => auth()->id(),
            'action_type' => 'restore',
            'keterangan'  => 'Membatalkan status siap musnah (Restore).'
        ]);

        return back()->with('success', 'Data berhasil dikembalikan (Restore).');
    }

    // 3. EKSEKUSI PEMUSNAHAN PERMANEN
    public function destroyPermanent($id)
    {
        $patient = Patient::findOrFail($id);
        
        // Tandai sebagai dimusnahkan
        $patient->update(['manual_status' => 'dimusnahkan']);

        RetentionAction::create([
            'patient_id'  => $id,
            'user_id'     => auth()->id(),
            'action_type' => 'eksekusi_musnah',
            'keterangan'  => 'Berkas fisik telah dimusnahkan/dicacah.'
        ]);

        return back()->with('success', 'Berkas TANDA DIMUSNAHKAN. Proses selesai.');
    }
}