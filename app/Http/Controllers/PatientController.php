<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PatientsImport;

class PatientController extends Controller
{
    // ==========================================
    // DATA WILAYAH (Bisa dipindah ke Config/Database nanti)
    // ==========================================
    private $nama_kecamatan = 'Sumbersari'; // Sesuaikan dengan Puskesmas kamu
    private $daftar_desa = [
        'Kel. Sumbersari',
        'Kel. Karangrejo',
        'Kel. Kebonsari',
        'Kel. Wirolegi',
        'Kel. Antirogo',
        'Kel. Tegalgede',
        'Kel. Kranjingan',
        'Luar Wilayah'
    ];

    public function index(Request $request)
    {
        $query = Patient::with('visits');

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Ambil data terbaru
        $patients = $query->latest()->get(); 

        // Filter Status (Manual Collection Filter)
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $patients = $patients->filter(function ($patient) use ($statusFilter) {
                // Pastikan di Model Patient ada accessor 'getCurrentStatusAttribute'
                return strtolower($patient->current_status) === strtolower($statusFilter);
            });
        }

        // Pagination Manual (Karena kita pakai filter collection di atas)
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $patients->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $patients = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($patients), $perPage);
        $patients->setPath($request->url());

        return view('patients.index', compact('patients'));
    }

    // --- REVISI BAGIAN CREATE ---
    public function create() 
    { 
        // Kita kirim data wilayah ke View biar jadi Dropdown
        $kecamatan = $this->nama_kecamatan;
        $daftar_desa = $this->daftar_desa;

        return view('patients.create', compact('kecamatan', 'daftar_desa')); 
    }

    // --- REVISI BAGIAN STORE ---
    public function store(Request $request) {
        $request->validate([
            'no_rm'           => 'required|unique:patients,no_rm', 
            'nik'             => 'required|numeric|digits:16|unique:patients,nik', // Tambah validasi NIK
            'nama_pasien'     => 'required|string|max:100', 
            'tgl_lahir'       => 'required|date', 
            'jenis_kelamin'   => 'required|in:L,P',
            
            // Validasi Alamat Baru
            'alamat_jalan'    => 'required|string',
            'desa_kelurahan'  => 'required|string',
            'kecamatan'       => 'required|string',
        ]);

        // Tentukan Kategori Wilayah Otomatis
        $data = $request->all();
        if ($request->desa_kelurahan == 'Luar Wilayah') {
            $data['kategori_wilayah'] = 'luar_wilayah';
        } else {
            $data['kategori_wilayah'] = 'dalam_wilayah';
        }

        Patient::create($data);
        
        return redirect()->route('patients.index')->with('success', 'Pasien berhasil ditambahkan');
    }

    // --- REVISI BAGIAN EDIT ---
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        
        // Kirim data desa juga ke halaman Edit, biar dropdown-nya muncul
        $kecamatan = $this->nama_kecamatan;
        $daftar_desa = $this->daftar_desa;

        return view('patients.edit', compact('patient', 'kecamatan', 'daftar_desa'));
    }

    // --- REVISI BAGIAN UPDATE ---
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $request->validate([
            'no_rm'           => 'required|string|unique:patients,no_rm,'.$id,
            'nik'             => 'required|digits:16|unique:patients,nik,'.$id,
            'nama_pasien'     => 'required|string|max:255',
            'tgl_lahir'       => 'required|date',
            'jenis_kelamin'   => 'required|in:L,P',
            
            // Validasi Alamat
            'alamat_jalan'    => 'required|string',
            'desa_kelurahan'  => 'required|string',
        ]);

        $data = $request->except(['tgl_kunjungan_terakhir', '_token', '_method']);

        // Update Kategori Wilayah jika desa berubah
        if ($request->desa_kelurahan == 'Luar Wilayah') {
            $data['kategori_wilayah'] = 'luar_wilayah';
        } else {
            $data['kategori_wilayah'] = 'dalam_wilayah';
        }

        // 1. Update Data Profil Pasien
        $patient->update($data);

        // 2. Update Kunjungan Terakhir (Opsional/Shortcut)
        if ($request->has('tgl_kunjungan_terakhir') && $request->tgl_kunjungan_terakhir) {
            $lastVisit = $patient->lastVisit; // Pastikan relasi di Model Patient bernama lastVisit()

            if ($lastVisit) {
                $lastVisit->update([
                    'tgl_kunjungan' => $request->tgl_kunjungan_terakhir
                ]);
            } else {
                // Buat kunjungan baru jika belum ada history
                Visit::create([
                    'patient_id'    => $patient->id,
                    'tgl_kunjungan' => $request->tgl_kunjungan_terakhir,
                    'poli_tujuan'   => 'Umum', // Default dulu
                    'status'        => 'selesai', // Anggap data lama sudah selesai
                    'keluhan'       => 'Data Import/Migrasi',
                ]);
            }
        }

        return redirect()->route('patients.index')->with('success', 'Data Pasien diperbarui!');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete(); // Karena pakai constrained('...')->onDelete('cascade'), data kunjungan ikut terhapus otomatis di DB
        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil dihapus!');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new PatientsImport, $request->file('file'));
            return back()->with('success', 'Data Pasien berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal impor: ' . $e->getMessage()]);
        }
    }
    public function show($id)
    {
        // 1. Cari pasien berdasarkan ID
        $patient = \App\Models\Patient::findOrFail($id);

        // 2. Tampilkan ke halaman detail
        // Pastikan nanti kamu buat file view-nya di resources/views/patients/show.blade.php
        return view('patients.show', compact('patient'));
    }
}