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
        $query = Patient::with('visits');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        $patients = $query->latest()->get(); 
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $patients = $patients->filter(fn($p) => strtolower($p->current_status) === strtolower($statusFilter));
        }
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $patients = new \Illuminate\Pagination\LengthAwarePaginator(
            $patients->slice(($currentPage - 1) * $perPage, $perPage)->all(),
            count($patients), $perPage, $currentPage, ['path' => $request->url()]
        );
        return view('patients.index', compact('patients'));
    }

    public function show($id)
    {
        $patient = Patient::with(['visits', 'actions'])->findOrFail($id);
        $years = $patient->lastVisit ? $patient->lastVisit->tgl_kunjungan->diffInYears(now()) : 0;
        return view('patients.show', compact('patient', 'years'));
    }

    public function create() { return view('patients.create'); }

    /**
     * LOGIKA HYBRID: Simpan Pasien + Kunjungan (Jika dipilih)
     */
    public function store(Request $request) 
    {
        // 1. Validasi Dasar Pasien
        $rules = [
            'no_rm'         => 'required|unique:patients,no_rm', 
            'nama_pasien'   => 'required|string|max:100', 
            'nik'           => 'nullable|string|max:20',
            'tgl_lahir'     => 'required|date', 
            'jenis_kelamin' => 'required|in:L,P',
            'alamat_lengkap'=> 'nullable|string'
        ];

        // 2. Jika user mencentang 'catat_kunjungan', tambahkan validasi kunjungan
        if ($request->has('catat_kunjungan')) {
            $rules['tgl_kunjungan'] = 'required|date';
            $rules['poli_tujuan']   = 'required|string';
            $rules['nama_dokter']   = 'required|string';
            $rules['diagnosa']      = 'required|string';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // 3. Simpan Data Pasien
            $patient = Patient::create($request->only([
                'no_rm', 'nama_pasien', 'nik', 'tgl_lahir', 'jenis_kelamin', 'alamat_lengkap'
            ]));

            // 4. Simpan Data Kunjungan (Jika ada)
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
            return redirect()->route('patients.index')->with('success', 'Pasien' . ($request->has('catat_kunjungan') ? ' & Kunjungan Pertama' : '') . ' berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Store Patient Hybrid: " . $e->getMessage());
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
        
        // Logika update kunjungan terakhir (Jika ada perubahan manual)
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