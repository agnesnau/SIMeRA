<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\RetentionAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini biar \DB::transaction aman

class PemilahanController extends Controller
{
    public function index(Request $request)
    {
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

        // Pastikan nama file view Anda benar (misal: retention/pemilahan.blade.php)
        return view('retention.pemilahan', compact('patients'));
    }

    /**
     * PERBAIKAN: finishSorting (Satuan) disamakan logic-nya dengan Massal
     */
    public function finishSorting($id)
    {
        $patient = Patient::findOrFail($id);
        
        // UBAH KE 'digudang' (Bukan NULL) agar konsisten dengan fitur massal
        $patient->update(['manual_status' => 'digudang']);

        RetentionAction::create([
            'patient_id'  => $id,
            'user_id'     => auth()->id(),
            'action_type' => 'masuk_gudang', // Samakan juga action_type-nya
            'keterangan'  => 'Proses Pemilahan Berkas Selesai. Berkas dipindahkan ke Gudang Inaktif.'
        ]);

        return redirect()->route('retensi.pemilahan')->with('success', 'Pemilahan selesai. Berkas resmi masuk Ruang Inaktif.');
    }

    public function bulkPemilahanRequest(Request $request)
    {
        $ids = explode(',', $request->ids);
        if (empty($ids) || $request->ids == "") {
            return back()->with('error', 'Pilih data pasien terlebih dahulu.');
        }

        Patient::whereIn('id', $ids)->update(['manual_status' => 'pemilahan']);
        
        return back()->with('success', count($ids) . ' berkas dipindahkan ke Pemilahan.');
    }

    public function finishAllSorting()
    {
        $patientsInSorting = Patient::where('manual_status', 'pemilahan')->get();

        if ($patientsInSorting->isEmpty()) {
            return back()->with('error', 'Tidak ada berkas yang perlu diproses.');
        }

        DB::transaction(function () use ($patientsInSorting) {
            
            // A. Update Status Pasien secara massal
            Patient::where('manual_status', 'pemilahan')
                   ->update(['manual_status' => 'digudang']);

            // B. Catat Log
            foreach ($patientsInSorting as $p) {
                RetentionAction::create([
                    'patient_id'  => $p->id,
                    'user_id'     => auth()->id(),
                    'action_type' => 'masuk_gudang', 
                    'keterangan'  => 'Berkas selesai dipilah dan resmi masuk Gudang Inaktif.'
                ]);
            }
        });

        return back()->with('success', 'Seluruh berkas telah dipindahkan ke Gudang Inaktif!');
    }
}