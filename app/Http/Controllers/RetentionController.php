<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\RetentionAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Tambahkan ini

class RetentionController extends Controller
{
    // ... Function index() TETAP SAMA ...
    public function index(Request $request)
    {
        // ... (Kode index sama seperti sebelumnya) ...
        // Agar singkat, saya tidak tulis ulang bagian index yg panjang ini
        // Pastikan variabel $paginatedPatients dan $stats tetap ada
        $patients = Patient::with('lastVisit')->latest()->get();

        if ($request->filled('status')) {
            $statusFilter = $request->status;
            if ($statusFilter == 'Verified') {
                $verifiedIds = RetentionAction::where('action_type', 'verifikasi_fisik')->pluck('patient_id')->toArray();
                $patients = $patients->whereIn('id', $verifiedIds);
            } else {
                $patients = $patients->filter(function ($patient) use ($statusFilter) {
                    return $patient->current_status === $statusFilter;
                });
            }
        }

        $stats = [
            'total_inaktif' => $patients->where('current_status', 'Inaktif')->count(),
            'siap_musnah'   => $patients->where('current_status', 'Siap Musnah')->count(),
        ];

        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $patients->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedPatients = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($patients), $perPage);
        $paginatedPatients->setPath($request->url());

        return view('retention.index', compact('paginatedPatients', 'stats'));
    }

    // UPDATE: LOGIKA UPLOAD & VERIFIKASI
    public function verifyPhysical(Request $request, $id)
    {
        // 1. Validasi File
        $request->validate([
            'file_nilai_guna' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        // 2. Upload File
        $filePath = null;
        if ($request->hasFile('file_nilai_guna')) {
            // Simpan di folder: storage/app/public/nilai_guna
            $filePath = $request->file('file_nilai_guna')->store('nilai_guna', 'public');
        }

        // 3. Simpan ke Database
        RetentionAction::create([
            'patient_id'  => $id,
            'user_id'     => auth()->id(),
            'action_type' => 'verifikasi_fisik',
            'keterangan'  => 'Berkas dinilai guna & diverifikasi.',
            'file_path'   => $filePath, // Simpan path
        ]);

        return back()->with('success', 'Nilai guna berhasil diupload & diverifikasi.');
    }
    
    // ... Function moveToDestruction() TETAP SAMA ...
    public function moveToDestruction($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->update(['manual_status' => 'siap_musnah']);

        RetentionAction::create([
            'patient_id'  => $id,
            'user_id'     => auth()->id(),
            'action_type' => 'ajukan_musnah',
            'keterangan'  => 'Berkas dipindahkan ke daftar siap musnah.'
        ]);
        
        return back()->with('success', 'Berkas dipindahkan ke daftar SIAP MUSNAH.');
    }
}