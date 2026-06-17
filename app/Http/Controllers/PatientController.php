<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use App\Models\RetentionAction;
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

        // 2. Logika Pencarian Nama/RM/NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // 3. LOGIKA FILTER STATUS
        if ($request->filled('status')) {
            $status = $request->status;

            if ($status === 'digudang') {
                $query->where('manual_status', 'digudang');
            } elseif ($status === 'pemilahan') {
                $query->where('manual_status', 'pemilahan');
            } elseif ($status === 'dimusnahkan') {
                 $query->where('manual_status', 'dimusnahkan');
            } else {
                // KECUALIKAN yang sudah diproses agar tidak tumpang tindih
                $query->where(function($q) {
                    $q->whereNotIn('manual_status', ['digudang', 'pemilahan', 'siap_musnah', 'dimusnahkan'])
                      ->orWhereNull('manual_status');
                });

                if ($status === 'Aktif') {
                    $query->where(function($q) {
                        $q->whereHas('lastVisit', function($q2) {
                            $q2->where('tgl_kunjungan', '>', now()->subYears(2));
                        })->orWhereDoesntHave('lastVisit');
                    });
                    
                } elseif ($status === 'Inaktif') {
                    $query->whereHas('lastVisit', function($q) {
                        $q->where('tgl_kunjungan', '<=', now()->subYears(2))
                          ->where('tgl_kunjungan', '>', now()->subYears(4)); 
                    });

                } elseif ($status === 'Siap Musnah') {
                    $query->whereHas('lastVisit', function($q) {
                        $q->where('tgl_kunjungan', '<=', now()->subYears(4));
                    });
                }
            }
        }

        // 4. MENGATUR JUMLAH DATA PER HALAMAN (CARA PALING OPTIMAL)
        $perPageRequest = $request->input('per_page', 10);
        
        if ($perPageRequest === 'all') {
            // Jika 'all', hitung total yang ada di query, minimal 1
            $totalData = $query->count();
            $perPage = $totalData > 0 ? $totalData : 1; 
        } else {
            // Pastikan menjadi integer
            $perPage = (int) $perPageRequest;
        }

        // Eksekusi paginasi langsung dari database, sangat ringan untuk RAM Server!
        $patients = $query->paginate($perPage);

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
            $rules['nama_dokter']   = 'required|string';
            $rules['diagnosa']      = 'required|string';
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
                    'dokter'        => $request->nama_dokter,
                    'diagnosa'      => $request->diagnosa,
                    'user_id'       => auth()->id(),
                ]);
            }

            DB::commit();
            return redirect()->route('patients.index')->with('success', 'Pasien berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id) { return view('patients.edit', ['patient' => Patient::findOrFail($id)]); }

    public function update(Request $request, $id) 
    {
        $patient = Patient::findOrFail($id);
        
        $request->validate([
            'no_rm'         => 'required|string|unique:patients,no_rm,'.$id,
            'nama_pasien'   => 'required',
            'tgl_lahir'     => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'nik'           => 'nullable|string',
        ]);

        $patient->update($request->only(['no_rm', 'nik', 'nama_pasien', 'tgl_lahir', 'jenis_kelamin', 'alamat_lengkap']));
        
        if ($request->filled('tgl_kunjungan_terakhir')) {
            $patient->update(['manual_status' => null]); 
            $lastVisit = $patient->lastVisit;
            $dataVisit = [
                'tgl_kunjungan' => $request->tgl_kunjungan_terakhir,
                'poli_tujuan'   => $request->poli_tujuan ?? 'Umum',
                'dokter'        => $request->nama_dokter ?? 'Admin Update',
                'diagnosa'      => $request->diagnosa_terakhir ?? 'Update Manual',
            ];

            if ($lastVisit) {
                $lastVisit->update($dataVisit);
            } else {
                Visit::create(array_merge($dataVisit, [
                    'no_registrasi' => 'MAN-'.time(), 
                    'patient_id' => $patient->id, 
                    'user_id' => auth()->id()
                ]));
            }
        }
        return redirect()->route('patients.index')->with('success', 'Data diperbarui!');
    }

    public function destroy($id) {
        $p = Patient::findOrFail($id);
        RetentionAction::where('patient_id', $id)->delete();
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
        
        try {
            DB::beginTransaction();
            RetentionAction::whereIn('patient_id', $ids)->delete();
            Visit::whereIn('patient_id', $ids)->delete();
            Patient::whereIn('id', $ids)->delete();
            DB::commit();
            return back()->with('success', count($ids) . ' data dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus massal: ' . $e->getMessage());
        }
    }
}