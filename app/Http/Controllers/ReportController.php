<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\GeneratedReport; 
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; 

class ReportController extends Controller
{
    /**
     * PROTEKSI AKSES: Hanya Admin/Kepala yang boleh masuk.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user() && auth()->user()->level === 'petugas') {
                return redirect('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman Pelaporan.');
            }
            return $next($request);
        });
    }

    /**
     * 1. TAMPILKAN MENU PELAPORAN & RIWAYAT
     */
    public function index()
    {
        // === [UPDATE LOGIKA HITUNGAN DASHBOARD] ===
        // Sesuai Request: "JUMLAH INAKTIF HANYA DARI PEMILAHAN BERKAS"
        
        // 1. Total Inaktif (Kandidat Retensi) = HANYA yang statusnya 'pemilahan'
        $totalInaktif = Patient::where('manual_status', 'pemilahan')->count();

        // 2. Siap Musnah = HANYA yang statusnya 'siap_musnah'
        $siapMusnah   = Patient::where('manual_status', 'siap_musnah')->count();

        // 3. Sudah Musnah
        $sudahMusnah  = Patient::where('manual_status', 'dimusnahkan')->count();
        
        $lokasi       = "Puskesmas Silo 1";

        // Ambil 20 riwayat laporan terakhir
        $history = GeneratedReport::latest()->take(20)->get();

        // Arahkan ke view laporan/index.blade.php
        return view('laporan.index', compact('siapMusnah', 'sudahMusnah', 'totalInaktif', 'lokasi', 'history'));
    }

    /**
     * 2. PROSES CETAK BERITA ACARA (PDF) & SIMPAN KE RIWAYAT
     */
    public function printBeritaAcara(Request $request)
    {
        // Ambil Input
        $jenis_ba = $request->input('jenis_ba', 'retensi');
        $no_surat = $request->input('no_surat');
        $tanggal  = $request->input('tanggal', date('Y-m-d'));
        $rentang_tahun = $request->input('rentang_tahun', '-');
        $sk_kapus = $request->input('sk_kapus', '-'); 
        $metode   = $request->input('metode', '-');

        // Mapping Variable Kosong
        $nama_p1 = ""; $nip_p1 = ""; $jabatan_p1 = "";
        $nama_p2 = ""; $nip_p2 = ""; $jabatan_p2 = "";
        $nama_kapus = ""; $nip_kapus = "";
        $data_berkas = collect([]);

        // === [LOGIKA PENGAMBILAN DATA CETAK] ===
        if ($jenis_ba == 'retensi') {
            // BA RETENSI -> Ambil dari 'pemilahan'
            $nama_p1    = $request->input('nama_p1'); 
            $nip_p1     = $request->input('nip_p1');
            $nama_p2    = $request->input('nama_p2'); 
            $nip_p2     = $request->input('nip_p2');
            $nama_kapus = $request->input('nama_tu'); 
            $nip_kapus  = $request->input('nip_tu');
            
            $jabatan_p1 = "Kasubag Tata Usaha";
            $jabatan_p2 = "Penanggung Jawab Rekam Medis";

            // Query: HANYA yang statusnya 'pemilahan'
            $data_berkas = Patient::where('manual_status', 'pemilahan')
                                  ->with('lastVisit')
                                  ->get();
        } else {
            // BA PEMUSNAHAN -> Ambil dari 'siap_musnah'
            $nama_p1    = $request->input('nama_ketua'); 
            $nip_p1     = "-"; 
            $nama_p2    = ""; 
            $nip_p2     = "";
            $nama_kapus = $request->input('nama_kapus');
            $nip_kapus  = ""; 

            $jabatan_p1 = "Ketua Tim Pemusnah";
            $jabatan_p2 = "";

            // Query: HANYA yang statusnya 'siap_musnah' (atau sudah dimusnahkan)
            $data_berkas = Patient::whereIn('manual_status', ['siap_musnah', 'dimusnahkan'])
                                  ->with('lastVisit')
                                  ->get();
        }

        $total_berkas = $request->input('total_berkas') ?? $data_berkas->count();

        // Simpan/Update Report (Pakai updateOrCreate biar gak error Duplicate)
        GeneratedReport::updateOrCreate(
            ['no_surat' => $no_surat],
            [
                'jenis_ba' => $jenis_ba,
                'tanggal_ba' => $tanggal,
                'total_berkas' => $total_berkas,
                'dibuat_oleh' => auth()->user()->nama_lengkap ?? 'Admin',
                'payload_data' => $request->all()
            ]
        );

        // Fix Nama File
        $safe_filename = str_replace(['/', '\\'], '_', $no_surat);

        // Generate PDF
        return Pdf::loadView('laporan.cetak', compact(
            'jenis_ba', 'no_surat', 'tanggal', 'rentang_tahun',
            'nama_p1', 'nip_p1', 'jabatan_p1',
            'nama_p2', 'nip_p2', 'jabatan_p2',
            'nama_kapus', 'nip_kapus',
            'data_berkas', 'total_berkas', 'sk_kapus', 'metode'
        ))->setPaper('A4', 'portrait')->stream("Berita_Acara_{$safe_filename}.pdf");
    }

    /**
     * 3. FUNGSI REPRINT
     */
    public function reprint($id)
    {
        $report = GeneratedReport::findOrFail($id);
        $data = $report->payload_data; 
        
        // Re-Fetch Data Berkas sesuai Logika Baru
        if ($report->jenis_ba == 'retensi') {
            // Reprint Retensi -> Cari yang 'pemilahan'
            $data['data_berkas'] = Patient::where('manual_status', 'pemilahan')
                                          ->with('lastVisit')
                                          ->get();
        } else {
            // Reprint Musnah -> Cari yang 'siap_musnah'/'dimusnahkan'
            $data['data_berkas'] = Patient::whereIn('manual_status', ['siap_musnah', 'dimusnahkan'])
                                          ->with('lastVisit')
                                          ->get();
        }

        $data['total_berkas'] = $report->total_berkas;

        $safe_filename = str_replace(['/', '\\'], '_', $report->no_surat);

        return Pdf::loadView('laporan.cetak', $data)
            ->setPaper('A4', 'portrait')
            ->stream("REPRINT_{$safe_filename}.pdf");
    }

    /**
     * 4. BULK RETENSI (Kode Lama)
     */
    public function bulkRetensi(Request $request)
    {
        $ids = explode(',', $request->ids);
        if (empty($ids) || $request->ids == "") {
            return back()->with('error', 'Pilih data pasien terlebih dahulu.');
        }

        // Ubah status ke 'pemilahan' agar masuk hitungan 'Inaktif' di Laporan
        Patient::whereIn('id', $ids)->update(['manual_status' => 'pemilahan']);
        
        return back()->with('success', count($ids) . ' berkas dipindahkan ke Pemilahan.');
    }
}