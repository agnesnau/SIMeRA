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
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $restrictedActions = ['create', 'store', 'edit', 'update', 'destroy', 'import', 'bulkAction'];
            if (auth()->user()->level === 'kepala' && in_array($request->route()->getActionMethod(), $restrictedActions)) {
                return redirect()->route('patients.index')->with('error', 'Akses Ditolak! View Only.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // 1. Query Dasar
        $query = Patient::with('lastVisit')->latest();

        // 2. Logika Pencarian Nama/RM
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }

        // 3. LOGIKA FILTER STATUS (YANG SUDAH DIPERBAIKI)
        if ($request->filled('status')) {
            $status = $request->status;
            $now = now();

            // A. Filter Status Manual (Gudang/Pemilahan/Musnah)
            if ($status === 'digudang') {
                $query->where('manual_status', 'digudang');
                
            } elseif ($status === 'pemilahan') {
                $query->where('manual_status', 'pemilahan');
                
            } elseif ($status === 'dimusnahkan') {
                 $query->where('manual_status', 'dimusnahkan');

            } else {
                // B. Filter Status Otomatis (Aktif/Inaktif/Siap Musnah)
                // PERBAIKAN PENTING DI SINI:
                // Kita gunakan logic "WHERE NOT IN" agar data yang manual_status-nya NULL, String Kosong, atau Spasi tetap terbaca.
                
                $query->where(function($q) {
                    $q->whereNotIn('manual_status', ['digudang', 'pemilahan', 'siap_musnah', 'dimusnahkan'])
                      ->orWhereNull('manual_status');
                });

                // Filter Berdasarkan Tanggal
                if ($status === 'Aktif') {
                    // Kunjungan < 2 Tahun
                    $query->whereHas('lastVisit', function($q) use ($now) {
                        $q->where('tgl_kunjungan', '>', $now->subYears(2));
                    });
                    
                } elseif ($status === 'Inaktif') {
                    // Kunjungan Antara 2 - 5 Tahun
                    // Misri (2022) akan masuk di sini karena > 2021 dan <= 2024 (Jika sekarang 2026)
                    $query->whereHas('lastVisit', function($q) use ($now) {
                        $q->where('tgl_kunjungan', '<=', $now->copy()->subYears(2))
                          ->where('tgl_kunjungan', '>', $now->copy()->subYears(5));
                    });

                } elseif ($status === 'Siap Musnah') {
                    // Kunjungan > 5 Tahun
                    $query->whereHas('lastVisit', function($q) use ($now) {
                        $q->where('tgl_kunjungan', '<=', $now->subYears(5));
                    });
                }
            }
        }

        $patients = $query->paginate(10);

        return view('patients.index', compact('patients'));
    } 

    public function show($id)
    {
        $patient = Patient::with(['visits', 'actions'])->findOrFail($id);
        $years = $patient->lastVisit ? $patient->lastVisit->tgl_kunjungan->diffInYears(now()) : 0;
        return view('patients.show', compact('patient', 'years'));
    }

    public function create() { return view('patients.create'); }

    public function store(Request $request) 
    {
        $rules = [
            'no_rm'         => 'required|unique:patients,no_rm', 
            'nama_pasien'   => 'required|string|max:100', 
            'nik'           => 'nullable|string|max:20',
            'tgl_lahir'     => 'required|date', 
            'jenis_kelamin' => 'required|in:L,P',
            'alamat_lengkap'=> 'nullable|string'
        ];

        if ($request->has('catat_kunjungan')) {
            $rules['tgl_kunjungan'] = 'required|date';
            $rules['poli_tujuan']   = 'required|string';
            $rules['pembayaran']    = 'required|in:BPJS,UMUM';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $patient = Patient::create($request->only([
                'no_rm', 'nama_pasien', 'nik', 'tgl_lahir', 'jenis_kelamin', 'alamat_lengkap'
            ]));

            if ($request->has('catat_kunjungan')) {
                Visit::create([
                    'no_registrasi' => 'REG-' . date('Ymd') . '-' . rand(100, 999),
                    'patient_id'    => $patient->id,
                    'tgl_kunjungan' => $request->tgl_kunjungan,
                    'poli_tujuan'   => $request->poli_tujuan,
                    'pembayaran'    => $request->pembayaran,
                    'user_id'       => auth()->id(),
                ]);
            }

            DB::commit();
            return redirect()->route('patients.index')->with('success', 'Pasien berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Store Patient: " . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id) { return view('patients.edit', ['patient' => Patient::findOrFail($id)]); }

    public function update(Request $request, $id) 
    {
        $patient = Patient::findOrFail($id);
        $request->validate([
            'no_rm' => 'required|string|unique:patients,no_rm,'.$id,
            'nama_pasien' => 'required',
            'tgl_lahir' => 'required|date',
        ]);
        $patient->update($request->only(['no_rm', 'nik', 'nama_pasien', 'tgl_lahir', 'jenis_kelamin', 'alamat_lengkap']));
        
        // Reset status manual jika ada update kunjungan
        if ($request->filled('tgl_kunjungan_terakhir')) {
            $patient->update(['manual_status' => null]); 
            $lastVisit = $patient->lastVisit;
            $dataVisit = [
                'tgl_kunjungan' => $request->tgl_kunjungan_terakhir,
                'poli_tujuan'   => $request->poli_tujuan ?? 'Umum',
                'pembayaran'    => $request->pembayaran ?? 'UMUM',
            ];

            if ($lastVisit) {
                $lastVisit->update($dataVisit);
            } else {
                Visit::create(array_merge($dataVisit, ['no_registrasi' => 'MAN-'.time(), 'patient_id' => $patient->id, 'user_id' => auth()->id()]));
            }
        }
        return redirect()->route('patients.index')->with('success', 'Data diperbarui!');
    }

    public function destroy($id) {
        $p = Patient::findOrFail($id);
        $p->visits()->delete();
        $p->delete();
        return back()->with('success', 'Data dihapus.');
    }

    public function import(Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);
        try {
            Excel::import(new PatientsImport, $request->file('file'));
            return back()->with('success', 'Import Berhasil!');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function bulkAction(Request $request) {
        if (!$request->has('ids') || $request->action_type !== 'hapus') return back();
        $ids = explode(',', $request->ids);
        Visit::whereIn('patient_id', $ids)->delete();
        Patient::whereIn('id', $ids)->delete();
        return back()->with('success', count($ids) . ' data dihapus.');
    }
}