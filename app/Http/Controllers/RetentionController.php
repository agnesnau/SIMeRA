<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\RetentionAction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RetentionController extends Controller
{
    /**
     * Menampilkan Daftar Retensi Utama
     * LOGIKA: Tabel HANYA menampilkan data Inaktif (< 4 Tahun) yang butuh Pemilahan.
     */
    public function index(Request $request)
    {
        // 1. QUERY DASAR
        // Kita join dengan tabel visits agar bisa sorting berdasarkan tgl_kunjungan
        $query = Patient::query()
            ->with(['lastVisit', 'actions'])
            ->leftJoin('visits', function($join) {
                // Join subquery untuk mendapatkan kunjungan terakhir setiap pasien
                $join->on('patients.id', '=', 'visits.patient_id')
                     ->whereRaw('visits.id = (select max(id) from visits where visits.patient_id = patients.id)');
            })
            ->select('patients.*', 'visits.tgl_kunjungan as last_visit_date');

        // 2. LOGIKA PENCARIAN
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('patients.nama_pasien', 'like', "%{$search}%")
                  ->orWhere('patients.no_rm', 'like', "%{$search}%");
            });
        }

        // 3. LOGIKA SORTING (BARU DITAMBAHKAN)
        // Default sort 'desc' (Terbaru dulu)
        $sortDirection = $request->input('sort', 'desc'); 
        
        // Pastikan hanya menerima 'asc' atau 'desc' untuk keamanan
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Sorting berdasarkan kolom tgl_kunjungan dari tabel visits yang sudah di-join
        $query->orderBy('last_visit_date', $sortDirection);

        // 4. AMBIL SEMUA DATA (Untuk Perhitungan Status Dinamis)
        $patients = $query->get();

        // 5. HITUNG UMUR & TENTUKAN STATUS DINAMIS
        $processedPatients = $patients->map(function ($patient) {
            $lastVisitDate = $patient->lastVisit ? $patient->lastVisit->tgl_kunjungan : null;
            $years = $lastVisitDate ? Carbon::parse($lastVisitDate)->diffInYears(now()) : 0;

            if ($patient->manual_status) {
                $currentStatus = ucwords(str_replace('_', ' ', $patient->manual_status));
            } else {
                if ($years < 2) {
                    $currentStatus = 'Aktif';
                } elseif ($years >= 2 && $years < 5) {
                    $currentStatus = 'Inaktif';
                } else {
                    $currentStatus = 'Siap Musnah';
                }
            }

            $patient->calculated_years = $years;
            $patient->current_status = $currentStatus;

            return $patient;
        });

        // 6. FILTER: HANYA TAMPILKAN DATA RETENSI (BUANG YANG AKTIF)
        $processedPatients = $processedPatients->filter(function ($patient) {
            // Logika: Ambil kalau statusnya BUKAN 'Aktif'
            return $patient->current_status !== 'Aktif';
        });

        // 7. FILTER STATUS (Diterapkan setelah pemetaan status dinamis)
        if ($request->filled('status')) {
            $processedPatients = $processedPatients->where('current_status', $request->status);
        }

        // 8. HITUNG STATISTIK (Perlu query terpisah agar statistik tetap akurat meski di-filter/sort)
        // Kita query ulang raw data untuk statistik global
        $allPatientsForStats = Patient::with('lastVisit')->get()->map(function($p) {
            $last = $p->lastVisit ? $p->lastVisit->tgl_kunjungan : null;
            $y = $last ? Carbon::parse($last)->diffInYears(now()) : 0;
            
            if($p->manual_status == 'siap_musnah') return 'siap_musnah';
            if($p->manual_status == 'dimusnahkan') return 'dimusnahkan';
            if($p->manual_status == 'pemilahan') return 'pemilahan';

            if($y < 2) return 'aktif';
            if($y >= 2 && $y < 5) return 'inaktif';
            return 'siap_musnah';
        });

        $stats = [
            'total_aktif'   => $allPatientsForStats->filter(fn($s) => $s == 'aktif')->count(),
            'total_inaktif' => $allPatientsForStats->filter(fn($s) => $s == 'inaktif')->count(),
            'siap_musnah'   => $allPatientsForStats->filter(fn($s) => $s == 'siap_musnah')->count(),
        ];

        // 9. PAGINATION MANUAL
        // Penting: Saat melakukan pagination pada Collection yang sudah di-sort query-nya, 
        // urutannya akan tetap terjaga.
        $page = $request->input('page', 1);
        $perPage = 10;
        
        // Reset keys agar index array urut (penting untuk JSON/View looping)
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
     * PROSES PEMILAHAN: Pindahkan Inaktif ke Gudang (Menu Pemilahan)
     */
    public function sendToSorting($id)
    {
        $patient = Patient::findOrFail($id);
        
        // Tandai berkas masuk tahap pemilahan
        $patient->update(['manual_status' => 'pemilahan']);

        RetentionAction::create([
            'patient_id'  => $id,
            'user_id'     => auth()->id() ?? 1,
            'action_type' => 'pindah_pemilahan',
            'keterangan'  => 'Berkas inaktif diajukan untuk proses pemilahan fisik ke gudang.'
        ]);

        return back()->with('success', 'Berkas dikirim ke Meja Pemilahan.');
    }
}