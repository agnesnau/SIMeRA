<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\RetentionAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemilahanController extends Controller
{
    public function index(Request $request)
    {
        // AMBIL DATA, TAPI SEMBUNYIKAN YANG SUDAH MASUK BA FINAL (status_approval = 2)
        $query = Patient::with(['lastVisit', 'actions'])
                        ->where('manual_status', 'pemilahan')
                        ->where('status_approval', '!=', 2); // <--- INI KUNCI PENGHILANGNYA

        // (Logika pencarian / search tetap biarkan seperti aslinya)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest('updated_at')->paginate(10);
        return view('retention.pemilahan', compact('patients'));
    }

    public function approve(Request $request)
    {
        // SUDAH DITAMBAHKAN 'supervisor' AGAR KAPUSKESMAS BISA ACC
        $level = strtolower(auth()->user()->level);
        if (!in_array($level, ['kapuskesmas', 'kepala', 'supervisor'])) {
            return back()->with('error', 'Akses Ditolak! Hanya Kepala Puskesmas yang berhak menyetujui pemindahan arsip.');
        }

        $ids = explode(',', $request->ids);
        if (empty($ids) || $request->ids == "") {
            return back()->with('error', 'Tidak ada data pasien untuk disetujui.');
        }

        Patient::whereIn('id', $ids)->update(['status_approval' => 1]);

        return back()->with('success', count($ids) . ' berkas telah DISETUJUI Kapuskesmas. Petugas kini dapat memindahkan berkas ke gudang.');
    }

    /**
     * PETUGAS: Membatalkan berkas yang SUDAH DI-ACC karena tidak cocok dengan fisik rak
     */
    public function koreksiEksekusi($id)
    {
        $patient = Patient::findOrFail($id);

        // Hanya bisa dikoreksi jika statusnya masih pemilahan tapi sudah di-ACC
        if ($patient->manual_status === 'pemilahan' && $patient->status_approval == 1) {
            $patient->update([
                'manual_status' => 'aktif', // Kembalikan jadi aktif
                'status_approval' => 0      // Buka gembok ACC
            ]);

            // Catat log agar transparan
            RetentionAction::create([
                'patient_id'  => $patient->id,
                'user_id'     => auth()->id(),
                'action_type' => 'koreksi_data',
                'keterangan'  => 'Koreksi: Dibatalkan setelah ACC Kapuskesmas karena berkas fisik di rak masih aktif.'
            ]);

            return back()->with('success', 'Koreksi berhasil! Berkas dikembalikan ke status Aktif.');
        }

        return back()->with('error', 'Koreksi gagal. Berkas tidak valid untuk dikoreksi.');
    }
    
    public function finishAllSorting()
    {
        $patientsInSorting = Patient::where('manual_status', 'pemilahan')
                                    ->where('status_approval', 1)
                                    ->get();

        if ($patientsInSorting->isEmpty()) {
            return back()->with('error', 'Tidak ada berkas yang siap diproses atau berkas belum di-ACC oleh Kepala Puskesmas.');
        }

        DB::transaction(function () use ($patientsInSorting) {
            Patient::whereIn('id', $patientsInSorting->pluck('id'))
                   ->update(['manual_status' => 'digudang']);

            foreach ($patientsInSorting as $p) {
                RetentionAction::create([
                    'patient_id'  => $p->id,
                    'user_id'     => auth()->id(),
                    'action_type' => 'masuk_gudang', 
                    'keterangan'  => 'Berkas selesai dipilah dan resmi masuk Gudang Inaktif setelah disetujui Kapuskesmas.'
                ]);
            }
        });

        return back()->with('success', 'Berhasil! '. $patientsInSorting->count() .' berkas yang disetujui telah masuk ke Gudang Inaktif.');
    }
}