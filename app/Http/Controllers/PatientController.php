<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PatientsImport;

class PatientController extends Controller
{
    /**
     * Pengaturan Hak Akses: Membatasi Kepala Puskesmas agar View Only
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $restrictedActions = ['create', 'store', 'edit', 'update', 'destroy', 'import', 'bulkAction'];
            
            if (auth()->user()->level === 'kepala' && in_array($request->route()->getActionMethod(), $restrictedActions)) {
                return redirect()->route('patients.index')->with('error', 'Akses Ditolak! Kepala Puskesmas hanya memiliki hak akses Lihat Data (View Only).');
            }

            return $next($request);
        });
    }

    /**
     * Tampilan Utama dengan Pencarian dan Filter Status
     */
    public function index(Request $request)
    {
        $query = Patient::with('visits');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Ambil semua data dulu untuk difilter berdasarkan Accessor 'current_status'
        $patients = $query->latest()->get(); 

        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $patients = $patients->filter(function ($patient) use ($statusFilter) {
                return strtolower($patient->current_status) === strtolower($statusFilter);
            });
        }

        // Manual Pagination karena difilter dari Collection
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $patients->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $patients = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($patients), $perPage);
        $patients->setPath($request->url());

        return view('patients.index', compact('patients'));
    }

    public function create() 
    { 
        return view('patients.create'); 
    }

    public function store(Request $request) 
    {
        $request->validate([
            'no_rm' => 'required|unique:patients,no_rm', 
            'nama_pasien' => 'required', 
            'tgl_lahir' => 'required|date', 
            'jenis_kelamin' => 'required'
        ]);
        
        Patient::create($request->all());
        return redirect()->route('patients.index')->with('success', 'Pasien berhasil ditambahkan');
    }

    public function edit($id) 
    {
        $patient = Patient::findOrFail($id);
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, $id) 
    {
        $patient = Patient::findOrFail($id);
        $request->validate([
            'no_rm' => 'required|string|unique:patients,no_rm,'.$id,
            'nama_pasien' => 'required',
            'tgl_lahir' => 'required|date',
        ]);
        
        $patient->update($request->except('tgl_kunjungan_terakhir'));

        // Logika update atau create kunjungan terakhir secara manual
        if ($request->filled('tgl_kunjungan_terakhir')) {
            $lastVisit = $patient->lastVisit;
            if ($lastVisit) {
                $lastVisit->update(['tgl_kunjungan' => $request->tgl_kunjungan_terakhir]);
            } else {
                Visit::create([
                    'no_registrasi' => 'MAN-' . time(),
                    'patient_id' => $patient->id,
                    'tgl_kunjungan' => $request->tgl_kunjungan_terakhir,
                    'dokter' => 'Admin Edit',
                    'user_id' => auth()->id()
                ]);
            }
        }

        return redirect()->route('patients.index')->with('success', 'Data Pasien diperbarui!');
    }

    /**
     * Hapus Satuan dengan Proteksi Foreign Key
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $patient = Patient::findOrFail($id);

            // 1. Bersihkan tabel anak agar tidak error 1451
            DB::table('retention_actions')->where('patient_id', $id)->delete();
            Visit::where('patient_id', $id)->delete();

            // 2. Hapus data utama
            $patient->delete();

            DB::commit();
            return back()->with('success', 'Data pasien dan seluruh riwayatnya berhasil dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal Hapus Pasien: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Import Data dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new PatientsImport, $request->file('file'));
            return back()->with('success', 'Data Pasien BERHASIL diimpor!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->withErrors(['file' => 'Gagal Validasi: Cek baris ke-' . $failures[0]->row()]);
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal Import: ' . $e->getMessage()]);
        }
    }

    /**
     * Aksi Massal (Hapus/Cetak)
     */
    public function bulkAction(Request $request)
    {
        if (!$request->has('ids') || !$request->has('action_type')) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $ids = explode(',', $request->ids);
        $action = $request->action_type;

        try {
            DB::beginTransaction();

            switch ($action) {
                case 'hapus':
                    // Bersihkan relasi dulu
                    DB::table('retention_actions')->whereIn('patient_id', $ids)->delete();
                    Visit::whereIn('patient_id', $ids)->delete();
                    
                    // Hapus Pasien
                    Patient::whereIn('id', $ids)->delete();
                    $message = count($ids) . " data pasien berhasil dihapus permanen.";
                    break;

                case 'cetak_kartu':
                    $message = "Fitur cetak kartu massal sedang disiapkan.";
                    break;

                default:
                    return back()->with('error', 'Tindakan tidak dikenal.');
            }

            DB::commit();
            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan Aksi Massal: ' . $e->getMessage());
        }
    }
}