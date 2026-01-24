<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // <--- WAJIB ADA BIAR BISA CETAK PDF

class ReportController extends Controller
{
    /**
     * 1. TAMPILKAN MENU PELAPORAN
     */
    public function index()
    {
        // Ambil data dan hitung status secara manual biar akurat
        // Kita hitung berdasarkan tahun kunjungan terakhir
        $patients = Patient::with('lastVisit')->get()->map(function($p) {
            $last = $p->lastVisit ? $p->lastVisit->tgl_kunjungan : null;
            $years = $last ? Carbon::parse($last)->diffInYears(now()) : 0;
            
            // Tentukan status sementara untuk statistik
            if($p->manual_status == 'siap_musnah') return 'Siap Musnah';
            if($p->manual_status == 'dimusnahkan') return 'Dimusnahkan';
            if($years >= 2 && $years < 5) return 'Inaktif';
            return 'Aktif';
        });
        
        $totalInaktif = $patients->where('Inaktif')->count(); // Logic Collection
        $siapMusnah   = $patients->filter(fn($s) => $s == 'Siap Musnah')->count();
        $sudahMusnah  = $patients->filter(fn($s) => $s == 'Dimusnahkan')->count();
        $lokasi       = "Puskesmas Silo 1";

        return view('laporan.index', compact('siapMusnah', 'sudahMusnah', 'totalInaktif', 'lokasi'));
    }

    /**
     * 2. PROSES CETAK BERITA ACARA (PDF)
     */
    public function printBeritaAcara(Request $request)
{
    // 1. Ambil input umum
    $jenis_ba = $request->input('jenis_ba', 'retensi');
    $no_surat = $request->input('no_surat');
    $tanggal  = $request->input('tanggal', date('Y-m-d'));
    $rentang_tahun = $request->input('rentang_tahun', '-');

    $sk_kapus = $request->input('sk_kapus', '-'); 
    $metode   = $request->input('metode', '-');

    // 2. Inisialisasi variabel agar tidak "Undefined"
    $nama_p1 = ""; $nip_p1 = ""; $jabatan_p1 = "";
    $nama_p2 = ""; $nip_p2 = ""; $jabatan_p2 = "";
    $nama_kapus = ""; $nip_kapus = "";

    if ($jenis_ba == 'retensi') {
        // Mapping data dari Form Retensi (name="nama_p1", dsb)
        $nama_p1    = $request->input('nama_p1'); 
        $nip_p1     = $request->input('nip_p1');
        $nama_p2    = $request->input('nama_p2'); 
        $nip_p2     = $request->input('nip_p2');
        $nama_kapus = $request->input('nama_tu'); // Di form retensi Anda memakai name="nama_tu" untuk Kapus
        $nip_kapus  = $request->input('nip_tu');
        
        $jabatan_p1 = "Kasubag Tata Usaha";
        $jabatan_p2 = "Penanggung Jawab Rekam Medis";
    } else {
        // Mapping data dari Form Pemusnahan (name="nama_saksi1", dsb)
        $nama_p1    = $request->input('nama_saksi1'); 
        $nip_p1     = $request->input('nip_saksi1');
        $nama_p2    = $request->input('nama_saksi2'); 
        $nip_p2     = $request->input('nip_saksi2');
        $nama_kapus = $request->input('nama_kapus');
        $nip_kapus  = $request->input('nip_kapus');

        $jabatan_p1 = "Saksi I (Internal)";
        $jabatan_p2 = "Saksi II (Eksternal)";
    }

    // 3. Filter data rekam medis untuk tabel lampiran
    $all_patients = Patient::with('lastVisit')->get();
    if ($jenis_ba == 'retensi') {
        $data_berkas = $all_patients->filter(function($p) {
            $lastDate = $p->lastVisit ? \Carbon\Carbon::parse($p->lastVisit->tgl_kunjungan) : null;
            $years = $lastDate ? $lastDate->diffInYears(now()) : 0;
            return $years >= 2 && $years < 5;
        });
    } else {
        $data_berkas = $all_patients->whereIn('manual_status', ['siap_musnah', 'dimusnahkan']);
    }

    $total_berkas = $request->input('total_berkas') ?? $data_berkas->count();

    // 4. Kirim semua variabel ke view melalui compact
    return Pdf::loadView('laporan.cetak', compact(
        'jenis_ba', 'no_surat', 'tanggal', 'rentang_tahun',
        'nama_p1', 'nip_p1', 'jabatan_p1',
        'nama_p2', 'nip_p2', 'jabatan_p2',
        'nama_kapus', 'nip_kapus',
        'data_berkas', 'total_berkas'
    ))->setPaper('A4', 'portrait')->stream('Berita_Acara.pdf');
    }
    public function bulkRetensi(Request $request)
{
    $ids = explode(',', $request->ids);
    
    if (empty($ids)) {
        return back()->with('error', 'Pilih data pasien terlebih dahulu.');
    }

    // Ubah status pasien yang dipilih menjadi 'Inaktif' (Retensi)
    // Kita gunakan update agar data tidak hilang (menghindari error Integrity Constraint)
    \App\Models\Patient::whereIn('id', $ids)->update([
        'manual_status' => 'Inaktif'
    ]);

    return back()->with('success', count($ids) . ' berkas berhasil dipindahkan ke status Inaktif.');
}
public function __construct()
{
    $this->middleware(function ($request, $next) {
        // Jika level penggunanya adalah petugas, blokir akses
        if (auth()->user()->level === 'petugas') {
            return redirect('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman Pelaporan.');
        }
        return $next($request);
    });
}
}