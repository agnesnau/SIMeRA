<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    /**
     * Membatasi akses Kepala Puskesmas agar hanya bisa melihat (View Only)
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $restrictedActions = ['create', 'store', 'edit', 'update', 'destroy', 'bulkAction'];
            
            if (auth()->user()->level === 'kepala' && in_array($request->route()->getActionMethod(), $restrictedActions)) {
                return redirect()->route('visits.index')->with('error', 'Akses Ditolak! Kepala Puskesmas hanya memiliki hak akses Lihat Data.');
            }

            return $next($request);
        });
    }

    /**
     * Menampilkan daftar kunjungan dengan pencarian, filter waktu, dan pengurutan.
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Query dengan Eager Loading History Pasien
        $query = Visit::with(['patient.visits' => function($q) {
            $q->latest('tgl_kunjungan');
        }]);

        // 2. Logika Pencarian Teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($sub) use ($search) {
                    $sub->where('nama_pasien', 'like', "%{$search}%")
                        ->orWhere('no_rm', 'like', "%{$search}%");
                })->orWhere('dokter', 'like', "%{$search}%")
                  ->orWhere('no_registrasi', 'like', "%{$search}%");
            });
        }

        // 3. LOGIKA FILTER WAKTU
        if ($request->filled('filter_time')) {
            $now = now();
            switch ($request->filter_time) {
                case 'minggu': 
                    $query->where('tgl_kunjungan', '>=', $now->subWeek()); 
                    break;
                case '3_bulan': 
                    $query->where('tgl_kunjungan', '>=', $now->subMonths(3)); 
                    break;
                case '6_bulan': 
                    $query->where('tgl_kunjungan', '>=', $now->subMonths(6)); 
                    break;
                case '1_tahun': 
                    $query->where('tgl_kunjungan', '>=', $now->subYear()); 
                    break;
                case '2_tahun': 
                    $query->where('tgl_kunjungan', '>=', $now->subYears(2)); 
                    break;
                case 'lebih_2_tahun': 
                    $query->where('tgl_kunjungan', '<', $now->subYears(2)); 
                    break;
            }
        }

        // 4. LOGIKA PENGURUTAN (Sort Order)
        // Default: 'desc' (Paling Baru)
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy('tgl_kunjungan', $sortOrder === 'asc' ? 'asc' : 'desc');

        // 5. Eksekusi Query dengan Pagination
        $visits = $query->paginate(10);

        return view('visits.index', compact('visits'));
    }

    /**
     * Menghapus satu data kunjungan
     */
    public function destroy($id)
    {
        $visit = Visit::findOrFail($id);
        $visit->delete();

        return back()->with('success', 'Data kunjungan berhasil dihapus.');
    }

    /**
     * Aksi Massal (Hapus banyak data sekaligus)
     */
    public function bulkAction(Request $request)
    {
        if (!$request->has('ids') || !$request->has('action_type')) {
            return back()->with('error', 'Pilih data dan tindakan terlebih dahulu.');
        }

        $ids = explode(',', $request->ids);
        
        if ($request->action_type === 'hapus') {
            Visit::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' data kunjungan berhasil dihapus massal.');
        }

        return back();
    }
}