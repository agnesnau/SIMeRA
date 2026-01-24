<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class RetentionController extends Controller
{
    /**
     * Menampilkan data pasien yang masuk kriteria Retensi (Inaktif & Siap Musnah)
     */
    public function index(Request $request)
    {
        // 1. Ambil data pasien beserta riwayat kunjungannya
        $query = Patient::with('visits');

        // 2. Logika Pencarian (No RM atau Nama)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }

        // 3. Ambil data ke Collection untuk filter berdasarkan Accessor 'current_status'
        $patients = $query->get();

        // 4. Filter Status: Hanya tampilkan Inaktif dan Siap Musnah
        $patients = $patients->filter(function ($patient) {
            $status = $patient->current_status;
            return $status === 'Inaktif' || $status === 'Siap Musnah';
        });

        // 5. Filter Dropdown (Jika user memilih Inaktif saja atau Siap Musnah saja)
        if ($request->filled('status_retensi')) {
            $statusFilter = $request->status_retensi;
            $patients = $patients->filter(function ($patient) use ($statusFilter) {
                return $patient->current_status === $statusFilter;
            });
        }

        // 6. Logika Pengurutan (Berdasarkan Kunjungan Terakhir)
        $sortOrder = $request->get('sort_order', 'desc');
        $patients = $patients->sortBy(function($patient) {
            return $patient->lastVisit ? $patient->lastVisit->tgl_kunjungan : null;
        }, SORT_REGULAR, ($sortOrder === 'desc'));

        // 7. Manual Pagination (Karena data difilter dari Collection)
        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $currentItems = $patients->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $patients = new LengthAwarePaginator(
            $currentItems, 
            $patients->count(), 
            $perPage, 
            $currentPage, 
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('retention.index', compact('patients'));
    }

    /**
     * Menangani Aksi Massal (Bulk Action)
     */
    public function bulkAction(Request $request)
    {
        $ids = explode(',', $request->ids);
        $action = $request->action_type;

        if (empty($ids) || !$action) {
            return back()->with('error', 'Tidak ada data atau tindakan yang dipilih.');
        }

        try {
            DB::beginTransaction();

            switch ($action) {
                case 'pindahkan':
                    // Logika memindahkan ke status 'Siap Musnah' secara manual
                    // Asumsi kita menggunakan kolom 'manual_status' di tabel patients
                    Patient::whereIn('id', $ids)->update(['manual_status' => 'siap_musnah']);
                    $message = count($ids) . " berkas berhasil dipindahkan ke status SIAP MUSNAH.";
                    break;

                case 'nilai_guna':
                    // Menandai bahwa berkas ini memiliki nilai guna (tidak boleh dimusnahkan)
                    Patient::whereIn('id', $ids)->update(['manual_status' => 'nilai_guna']);
                    $message = count($ids) . " berkas ditandai memiliki NILAI GUNA.";
                    break;

                default:
                    return back()->with('error', 'Tindakan tidak dikenal.');
            }

            DB::commit();
            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}