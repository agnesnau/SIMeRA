<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\RetentionAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EksekusiController extends Controller
{
    /**
     * 1. TAMPILKAN DAFTAR (Termasuk yang sudah selesai biar bisa buat BA)
     */
    public function index(Request $request)
    {
        // Kita ambil status 'siap_musnah' DAN 'dimusnahkan' 
        // supaya yang baru diproses tidak langsung hilang
        $query = Patient::with(['lastVisit'])
                        ->whereIn('manual_status', ['siap_musnah', 'dimusnahkan']); 

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }

        // Urutkan berdasarkan yang paling baru diupdate
        $patients = $query->latest('updated_at')->paginate(10);
        return view('pemusnahan.eksekusi', compact('patients'));
    }

    /**
     * 2. PETUGAS: Bulk Action "Usul Eksekusi" (Dari Halaman Daftar Utama)
     */
    public function usulEksekusi(Request $request)
    {
        $ids = explode(',', $request->ids);
        if (empty($ids) || $request->ids == "") {
            return back()->with('error', 'Pilih data pasien terlebih dahulu.');
        }

        // Pindahkan pasien ke antrean "siap_musnah" dan KUNCI GEMBOK (0)
        Patient::whereIn('id', $ids)->update([
            'manual_status' => 'siap_musnah',
            'status_approval' => 0 
        ]);
        
        return back()->with('success', count($ids) . ' berkas berhasil diusulkan ke Menu Eksekusi. Menunggu persetujuan Kepala Puskesmas.');
    }

    /**
     * 3. KAPUSKESMAS: Tombol "Approval / Setujui" di Menu Eksekusi
     */
    public function approve(Request $request)
    {
        // AMBIL LEVEL USER SEKARANG, BERSIHKAN SPASI, DAN JADIKAN HURUF KECIL
        $userLevel = trim(strtolower(auth()->user()->level));

        // PROTEKSI: Cek apakah yang klik benar-benar Kapuskesmas atau Kepala
        $allowedLevels = ['kapuskesmas', 'kepala', 'supervisor'];
        
        if (!in_array($userLevel, $allowedLevels)) {
            return back()->with('error', 'Akses Ditolak! Hanya Kepala Puskesmas yang berhak memberikan persetujuan.');
        }

        $ids = explode(',', $request->ids);
        if (empty($ids) || $request->ids == "") {
            return back()->with('error', 'Pilih data pasien yang akan disetujui.');
        }

        // BUKA GEMBOK (Ubah status_approval jadi 1)
        Patient::whereIn('id', $ids)->update(['status_approval' => 1]);

        return back()->with('success', count($ids) . ' berkas telah DISETUJUI. Petugas kini dapat mengeksekusi dokumen.');
    }

    /**
     * 4. PETUGAS: Eksekusi Satuan (Pilih Nilai Guna & Selesai)
     */
    public function selesai(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        // PROTEKSI GEMBOK: Tolak kalau Kapuskesmas belum ACC
        if ($patient->status_approval == 0) {
            return back()->with('error', 'Gagal! Berkas ini belum disetujui oleh Kepala Puskesmas.');
        }

        // VALIDASI DINAMIS (Tidak memaksa upload kalau pilih "Tidak Ada")
        $request->validate([
            'status_nilai_guna' => 'required|in:ada,tidak',
            'file_nilai_guna'   => 'required_if:status_nilai_guna,ada|nullable|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'status_nilai_guna.required'  => 'Harap pilih status nilai guna!',
            'file_nilai_guna.required_if' => 'File Nilai Guna wajib diupload karena Anda memilih "Ada"!',
            'file_nilai_guna.mimes'       => 'Format file harus PDF, JPG, atau PNG.',
            'file_nilai_guna.max'         => 'Ukuran file maksimal 5MB.'
        ]);

        DB::transaction(function () use ($patient, $request) {
            $path = null;
            $keterangan = 'Berkas dimusnahkan (Fisik Sudah Dihancurkan). Tidak ada dokumen bernilai guna.';

            // Jika ada file dan dipilih "Ada", baru diproses simpannya
            if ($request->status_nilai_guna === 'ada' && $request->hasFile('file_nilai_guna')) {
                $path = $request->file('file_nilai_guna')->store('dokumen_nilai_guna', 'public');
                $keterangan = 'Berkas dimusnahkan. Dokumen Nilai Guna disimpan di: ' . $path;
            }

            // Ubah status jadi dimusnahkan tapi tetap masuk filter index
            $patient->update([
                'manual_status' => 'dimusnahkan',
                'status_approval' => 1 // Tetap 1 supaya tidak terkunci lagi
            ]);

            // Catat Log 
            RetentionAction::create([
                'patient_id'  => $patient->id,
                'user_id'     => auth()->id(),
                'action_type' => 'pemusnahan_fisik',
                'keterangan'  => $keterangan
            ]);
        });

        return back()->with('success', 'Berhasil! Status berubah jadi DIMUSNAHKAN. Data tetap tampil untuk keperluan cetak Berita Acara.');
    }

  /**
     * PETUGAS: Membatalkan berkas musnah yang SUDAH DI-ACC karena alasan tertentu
     */
    public function koreksiEksekusi($id)
    {
        $patient = Patient::findOrFail($id);

        if ($patient->manual_status === 'siap_musnah' && $patient->status_approval == 1) {
            $patient->update([
                'manual_status' => 'digudang', // Kembalikan ke gudang inaktif
                'status_approval' => 0         // Buka gembok ACC
            ]);

            RetentionAction::create([
                'patient_id'  => $patient->id,
                'user_id'     => auth()->id(),
                'action_type' => 'koreksi_data',
                'keterangan'  => 'Koreksi: Usul musnah dibatalkan setelah ACC Kapuskesmas.'
            ]);

            return back()->with('success', 'Koreksi berhasil! Berkas dikembalikan ke Gudang Inaktif.');
        }

        return back()->with('error', 'Koreksi gagal. Berkas tidak valid untuk dikoreksi.');
    }

    /**
     * 5. PETUGAS: Fitur "Musnahkan Semua" (Yang sudah di-ACC)
     */
    public function destroyAll()
    {
        // Ambil pasien yang statusnya 'siap_musnah' DAN status_approval = 1 (Sudah di-ACC)
        $patients = Patient::where('manual_status', 'siap_musnah')
                           ->where('status_approval', 1)
                           ->get();

        if ($patients->isEmpty()) {
            return back()->with('error', 'Tidak ada berkas yang siap dieksekusi, atau berkas belum disetujui Kepala Puskesmas.');
        }

        DB::transaction(function () use ($patients) {
            // Update jadi 'dimusnahkan'
            Patient::whereIn('id', $patients->pluck('id'))->update(['manual_status' => 'dimusnahkan']);

            // Catat Log Massal
            foreach ($patients as $p) {
                RetentionAction::create([
                    'patient_id'  => $p->id,
                    'user_id'     => auth()->id(),
                    'action_type' => 'pemusnahan_fisik',
                    'keterangan'  => 'Berkas fisik telah dimusnahkan (dicacah/bakar) secara massal sesuai SOP.'
                ]);
            }
        });

        return back()->with('success', 'Eksekusi Massal Selesai! '. $patients->count() .' berkas telah DIMUSNAHKAN.');
    }
    /**
     * PETUGAS: Membatalkan usulan musnah yang BELUM DI-ACC
     */
    public function batalUsul($id)
    {
        $patient = Patient::findOrFail($id);

        // Pastikan berkas masih berstatus siap musnah dan belum di-ACC
        if ($patient->manual_status === 'siap_musnah' && $patient->status_approval != 1) {
            
            // Kembalikan ke Gudang Inaktif
            $patient->update([
                'manual_status' => 'digudang',
                'status_approval' => 0 
            ]);

            // Catat ke Riwayat Log
            RetentionAction::create([
                'patient_id'  => $patient->id,
                'user_id'     => auth()->id(),
                'action_type' => 'batal_usul_musnah',
                'keterangan'  => 'Usul musnah dibatalkan oleh petugas. Berkas dikembalikan ke Gudang Inaktif.'
            ]);

            return back()->with('success', 'Berhasil! Usulan musnah ditarik dan berkas dikembalikan ke Gudang Inaktif.');
        }

        return back()->with('error', 'Gagal membatalkan! Berkas mungkin sudah terlanjur di-ACC atau dieksekusi.');
    }
}