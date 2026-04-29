<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\GeneratedReport; 
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
     * 1. HALAMAN MENU PELAPORAN
     */
    public function index(Request $request)
    {
        // AMBIL DARI TABEL RIWAYAT
        $query = GeneratedReport::query();

        // FITUR PENCARIAN
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                  ->orWhere('jenis_ba', 'like', "%{$search}%");
            });
        }

        $history = $query->latest()->paginate(10);
        return view('laporan.index', compact('history'));
    }

    /**
     * 2. PROSES CETAK (USULAN & BA FINAL) 
     */
    public function printBeritaAcara(Request $request)
    {
        $jenis_ba      = $request->input('jenis_ba', 'retensi');
        $tipe_dokumen  = $request->input('tipe_dokumen', 'berita_acara'); 
        $no_surat      = $request->input('no_surat');
        $tanggal       = $request->input('tanggal', date('Y-m-d'));
        $rentang_tahun = $request->input('rentang_tahun', '-');
        $sk_kapus      = $request->input('sk_kapus', '-'); 
        $metode        = $request->input('metode', '-');

        $nama_p1 = ""; $nip_p1 = ""; $jabatan_p1 = "";
        $nama_p2 = ""; $nip_p2 = ""; $jabatan_p2 = "";
        $nama_kapus = ""; $nip_kapus = "";
        
        $data_berkas = collect([]);

        // ====================================================================
        // LOGIKA PENGAMBILAN DATA (USULAN vs FINAL) - DIPERBAIKI TOTAL
        // ====================================================================
        if ($tipe_dokumen == 'pertelaan') { 
            // -------------------------------------------------------------
            // CETAK USULAN: Ambil semua yang BUKAN 1 (Menunggu ACC)
            // -------------------------------------------------------------
            if ($jenis_ba == 'retensi') {
                $data_berkas = Patient::where('manual_status', 'pemilahan')
                                      ->where(function($query) {
                                          $query->where('status_approval', '!=', 1)
                                                ->orWhereNull('status_approval');
                                      })->with('lastVisit')->get();
            } else {
                $data_berkas = Patient::where('manual_status', 'siap_musnah')
                                      ->where(function($query) {
                                          $query->where('status_approval', '!=', 1)
                                                ->orWhereNull('status_approval');
                                      })->with('lastVisit')->get();
            }

            if ($data_berkas->isEmpty()) {
                return back()->with('error', 'Gagal! Tidak ada daftar usulan yang menunggu persetujuan (ACC) saat ini.');
            }
            
        } else {
            // -------------------------------------------------------------
            // CETAK BA FINAL: Ambil yang SUDAH dieksekusi (Gudang/Musnah) dan statusnya 1
            // -------------------------------------------------------------
            if ($jenis_ba == 'retensi') {
                $data_berkas = Patient::where('manual_status', 'digudang')
                                      ->where('status_approval', 1)
                                      ->with('lastVisit')->get();
            } else {
                $data_berkas = Patient::where('manual_status', 'dimusnahkan')
                                      ->where('status_approval', 1)
                                      ->with('lastVisit')->get();
            }

            if ($data_berkas->isEmpty()) {
                return back()->with('error', 'Gagal Cetak! Data kosong. Pastikan petugas sudah menyelesaikan eksekusi pemindahan ke gudang atau pemusnahan fisik.');
            }
        }
        
        $total_berkas = $request->input('total_berkas') ?? $data_berkas->count();

        // ====================================================================
        // MAPPING TANDA TANGAN
        // ====================================================================
        if ($jenis_ba == 'retensi') {
            $nama_p1 = $request->input('nama_p1'); $nip_p1 = $request->input('nip_p1', '-');
            $nama_p2 = $request->input('nama_p2'); $nip_p2 = $request->input('nip_p2', '-');
            $nama_kapus = $request->input('nama_tu'); $nip_kapus = $request->input('nip_tu', '-');
            $jabatan_p1 = "Penanggung Jawab Rekam Medis"; $jabatan_p2 = "Kasubag Tata Usaha";
        } else {
            $nama_p1 = $request->input('nama_ketua'); $nip_p1 = $request->input('nip_ketua', '-'); 
            $nama_p2 = ""; $nip_p2 = "";
            $nama_kapus = $request->input('nama_kapus'); $nip_kapus = $request->input('nip_kapus', '-'); 
            $jabatan_p1 = "Ketua Tim Pemusnah"; $jabatan_p2 = "";
        }

        // ====================================================================
        // SIMPAN KE RIWAYAT BESERTA ID PASIEN
        // ====================================================================
        $payloadData = $request->all();
        $payloadData['patient_ids'] = $data_berkas->pluck('id')->toArray();
        $payloadData['tipe_dokumen'] = $tipe_dokumen; 

        GeneratedReport::updateOrCreate(
            ['no_surat' => $no_surat],
            [
                'jenis_ba' => $jenis_ba,
                'tanggal_ba' => $tanggal,
                'total_berkas' => $total_berkas,
                // 'tipe_dokumen' => $tipe_dokumen, // DIMATIKAN AGAR TIDAK ERROR SQL!
                'dibuat_oleh' => auth()->user()->nama_lengkap ?? 'Admin',
                'payload_data' => $payloadData
            ]
        );

        // ====================================================================
        // KUNCI PERMANEN JIKA CETAK FINAL
        // ====================================================================
        if ($tipe_dokumen == 'berita_acara') {
            Patient::whereIn('id', $payloadData['patient_ids'])->update(['status_approval' => 2]);
        }

        $safe_filename = str_replace(['/', '\\'], '_', $no_surat);

        // RENDER PDF
        return Pdf::loadView('laporan.cetak', compact(
            'jenis_ba', 'no_surat', 'tanggal', 'rentang_tahun',
            'nama_p1', 'nip_p1', 'jabatan_p1',
            'nama_p2', 'nip_p2', 'jabatan_p2',
            'nama_kapus', 'nip_kapus',
            'data_berkas', 'total_berkas', 'sk_kapus', 'metode', 'tipe_dokumen'
        ))->setPaper('A4', 'portrait')->stream("{$tipe_dokumen}_{$safe_filename}.pdf");
    }

    /**
     * 3. FUNGSI REPRINT (CETAK ULANG DARI MENU RIWAYAT)
     */
    public function reprint($id)
    {
        $report = GeneratedReport::findOrFail($id);
        $payload = $report->payload_data; 
        
        $jenis_ba = $report->jenis_ba;
        $no_surat = $report->no_surat;
        $tanggal  = $report->tanggal_ba;
        $total_berkas = $report->total_berkas;

        $rentang_tahun = $payload['rentang_tahun'] ?? '-';
        $sk_kapus = $payload['sk_kapus'] ?? '-';
        $metode   = $payload['metode'] ?? '-';
        $tipe_dokumen = $payload['tipe_dokumen'] ?? 'berita_acara';

        $data_berkas = collect([]);
        if (isset($payload['patient_ids']) && !empty($payload['patient_ids'])) {
            $data_berkas = Patient::whereIn('id', $payload['patient_ids'])->with('lastVisit')->get();
        }

        $nama_p1 = ""; $nip_p1 = ""; $jabatan_p1 = "";
        $nama_p2 = ""; $nip_p2 = ""; $jabatan_p2 = "";
        $nama_kapus = ""; $nip_kapus = "";

        if ($jenis_ba == 'retensi') {
            $nama_p1 = $payload['nama_p1'] ?? ''; $nip_p1 = $payload['nip_p1'] ?? '-';
            $nama_p2 = $payload['nama_p2'] ?? ''; $nip_p2 = $payload['nip_p2'] ?? '-';
            $nama_kapus = $payload['nama_tu'] ?? ''; $nip_kapus = $payload['nip_tu'] ?? '-';
            $jabatan_p1 = "Penanggung Jawab Rekam Medis"; $jabatan_p2 = "Kasubag Tata Usaha";
        } else {
            $nama_p1 = $payload['nama_ketua'] ?? ''; $nip_p1 = $payload['nip_ketua'] ?? '-'; 
            $nama_kapus = $payload['nama_kapus'] ?? ''; $nip_kapus = $payload['nip_kapus'] ?? '-'; 
            $jabatan_p1 = "Ketua Tim Pemusnah";
        }

        $safe_filename = str_replace(['/', '\\'], '_', $no_surat);

        return Pdf::loadView('laporan.cetak', compact(
            'jenis_ba', 'no_surat', 'tanggal', 'rentang_tahun',
            'nama_p1', 'nip_p1', 'jabatan_p1',
            'nama_p2', 'nip_p2', 'jabatan_p2',
            'nama_kapus', 'nip_kapus',
            'data_berkas', 'total_berkas', 'sk_kapus', 'metode', 'tipe_dokumen'
        ))->setPaper('A4', 'portrait')->stream("REPRINT_{$safe_filename}.pdf");
    }
}