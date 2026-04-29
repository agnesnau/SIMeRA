<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit; 
use App\Models\RetentionAction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RetentionController extends Controller
{
    /**
     * Menampilkan Daftar Retensi Utama
     * Menampilkan data Inaktif (2-4 Tahun) yang belum masuk tahap pemilahan.
     * UPDATE: Exclude > 4 Tahun (sudah di halaman pemusnahan) dan < 2 Tahun (masih aktif).
     */
    public function index(Request $request)
    {
        // 1. Query dasar
        $query = Patient::query()
            ->with(['lastVisit', 'actions'])
            ->leftJoin('visits', function($join) {
                $join->on('patients.id', '=', 'visits.patient_id')
                     ->whereRaw('visits.id = (select max(id) from visits where visits.patient_id = patients.id)');
            })
            ->select('patients.*', 'visits.tgl_kunjungan as last_visit_date');

        // 2. Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('patients.nama_pasien', 'like', "%{$search}%")
                  ->orWhere('patients.no_rm', 'like', "%{$search}%");
            });
        }

        // 3. Sorting
        $sortDirection = $request->input('sort', 'desc'); 
        $query->orderBy('last_visit_date', $sortDirection);

        $patients = $query->get();

        // 4. Proses status dan perhitungan umur
        $processedPatients = $patients->map(function ($patient) {
            $lastVisitDate = $patient->lastVisit ? $patient->lastVisit->tgl_kunjungan : null;
            $years = $lastVisitDate ? Carbon::parse($lastVisitDate)->diffInYears(now()) : 0;

            if ($patient->manual_status) {
                $currentStatus = ucwords(str_replace('_', ' ', $patient->manual_status));
            } else {
                if ($years < 2) {
                    $currentStatus = 'Aktif';
                } elseif ($years >= 2 && $years < 4) {
                    $currentStatus = 'Inaktif';
                } else {
                    $currentStatus = 'Siap Musnah';
                }
            }

            $patient->calculated_years = $years;
            $patient->current_status = $currentStatus;
            return $patient;
        });

        // 5. FILTER UTAMA: 
        // HANYA STATUS 'INAKTIF' (2-4 TAHUN)
        // EXCLUDE 'AKTIF' (<2) DAN 'SIAP MUSNAH' (>4)
        $processedPatients = $processedPatients->filter(function ($patient) {
            return $patient->current_status === 'Inaktif' && $patient->manual_status !== 'pemilahan';
        });

        if ($request->filled('status')) {
            $processedPatients = $processedPatients->where('current_status', $request->status);
        }

        // 6. Statistik
        $allPatientsForStats = Patient::with('lastVisit')->get()->map(function($p) {
            $last = $p->lastVisit ? $p->lastVisit->tgl_kunjungan : null;
            $y = $last ? Carbon::parse($last)->diffInYears(now()) : 0;
            if($p->manual_status) return $p->manual_status;
            if($y < 2) return 'aktif';
            if($y >= 2 && $y < 4) return 'inaktif';
            return 'siap_musnah';
        });

        $stats = [
            'total_aktif'     => $allPatientsForStats->filter(fn($s) => $s == 'aktif')->count(),
            'total_inaktif'   => $allPatientsForStats->filter(fn($s) => $s == 'inaktif')->count(),
            'total_pemilahan' => $allPatientsForStats->filter(fn($s) => $s == 'pemilahan')->count(),
            'siap_musnah'     => $allPatientsForStats->filter(fn($s) => $s == 'siap_musnah')->count(),
        ];

        // 7. Pagination
        $page = $request->input('page', 1);
        $perPage = 10;
        $currentPageResults = $processedPatients->values()->slice(($page - 1) * $perPage, $perPage);

        $paginatedPatients = new LengthAwarePaginator(
            $currentPageResults,
            $processedPatients->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('retention.index', compact('paginatedPatients', 'stats'));
    }

    /**
     * PROSES SATUAN: Kirim ke Meja Pemilahan
     */
    public function sendToSorting($id)
    {
        $patient = Patient::findOrFail($id);
        
        // PERBAIKAN: SET STATUS_APPROVAL = 0 (GEMBOK TERTUTUP)
        $patient->update(['manual_status' => 'pemilahan', 'status_approval' => 0]);

        RetentionAction::create([
            'patient_id'  => $id,
            'user_id'     => auth()->id(),
            'action_type' => 'pindah_pemilahan',
            'keterangan'  => 'Berkas diusulkan untuk pindah ke Gudang Inaktif (Menunggu ACC).'
        ]);

        return back()->with('success', 'Usulan Pindah Gudang berhasil dikirim. Menunggu persetujuan Kapuskesmas.');
    }

    /**
     * AKSI MASSAL (Bulk Action)
     */
    public function bulkAction(Request $request) 
    {
        if (!$request->filled('ids')) {
            return back()->with('error', 'Silakan pilih pasien terlebih dahulu melalui checkbox.');
        }

        if (!$request->filled('action_type')) {
            return back()->with('error', 'Pilih tindakan yang ingin dilakukan pada data terpilih.');
        }
        
        $ids = explode(',', $request->ids);
        $jumlah = count($ids);

        if ($request->action_type === 'pindah') {
            
            // PERBAIKAN: SET STATUS_APPROVAL = 0 (GEMBOK TERTUTUP)
            Patient::whereIn('id', $ids)->update(['manual_status' => 'pemilahan', 'status_approval' => 0]);
            
            foreach($ids as $id) {
                RetentionAction::create([
                    'patient_id'  => $id,
                    'user_id'     => auth()->id(),
                    'action_type' => 'pindah_pemilahan',
                    'keterangan'  => 'Berkas diusulkan secara massal ke Gudang Inaktif (Menunggu ACC).'
                ]);
            }
            
            return back()->with('success', "Berhasil! Sebanyak $jumlah berkas telah diusulkan pindah ke Gudang Inaktif.");
        }

        return back()->with('error', 'Tindakan tidak dikenali oleh sistem.');
    }
}