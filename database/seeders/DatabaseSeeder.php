<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\RetentionAction;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. DATA USER (AKUN LOGIN)
        $admin = User::create([
            'nip'          => '198501012010011001',
            'nama_lengkap' => 'Administrator Sistem',
            'username'     => 'admin', 
            'password'     => Hash::make('123'),
            'level'        => 'admin'
        ]);

        $petugas = User::create([
            'nip'          => '199002022015022002',
            'nama_lengkap' => 'Petugas Rekam Medis',
            'username'     => 'petugas',
            'password'     => Hash::make('123'),
            'level'        => 'petugas'
        ]);

        // 2. DATA SPESIFIK UNTUK CONTOH (RM-001 s.d RM-010)
        // Tetap dipertahankan untuk kebutuhan demonstrasi status manual
        $this->createData('RM-001', 'Budi Santoso', 'L', now()->subMonths(3), 'Poli Umum', 'Febris', $petugas->id);
        $this->createData('RM-002', 'Ani Wijaya', 'P', now()->subMonths(10), 'Poli Gigi', 'Karies', $petugas->id);
        $this->createData('RM-003', 'Citra Kirana', 'P', now()->subYear(), 'KIA', 'Imunisasi', $petugas->id);
        $this->createData('RM-004', 'Dedi Corbuzier', 'L', now()->subYears(2)->subMonths(6), 'Poli Umum', 'Hipertensi', $petugas->id);
        $this->createData('RM-005', 'Eko Patrio', 'L', now()->subYears(3), 'IGD', 'Dyspepsia', $petugas->id);
        
        $p6 = $this->createData('RM-006', 'Fatin Shidqia', 'P', now()->subYears(3)->subMonths(11), 'Poli Gigi', 'Pulpitis', $petugas->id);
        RetentionAction::create(['patient_id' => $p6->id, 'user_id' => $admin->id, 'action_type' => 'verifikasi_fisik', 'keterangan' => 'Nilai guna diupload', 'file_path' => 'nilai_guna/sample.pdf']);

        $this->createData('RM-007', 'Gading Marten', 'L', now()->subYears(5), 'Poli Umum', 'Cephalgia', $petugas->id);
        $this->createData('RM-008', 'Hesti Purwadinata', 'P', now()->subYears(7), 'KIA', 'ANC Routine', $petugas->id);

        $p9 = Patient::create(['no_rm' => 'RM-009', 'nik' => '3509090909090009', 'nama_pasien' => 'Indro Warkop', 'tgl_lahir' => '1960-01-01', 'jenis_kelamin' => 'L', 'alamat_lengkap' => 'Jl. Jakarta No. 9', 'manual_status' => 'siap_musnah']);
        $this->addVisit($p9->id, now()->subYears(4)->subMonths(5), 'IGD', 'Asthma', $petugas->id);

        $p10 = Patient::create(['no_rm' => 'RM-010', 'nik' => '3501010101010010', 'nama_pasien' => 'Joko Anwar', 'tgl_lahir' => '1976-01-01', 'jenis_kelamin' => 'L', 'alamat_lengkap' => 'Jl. Surabaya No. 10', 'manual_status' => 'dimusnahkan']);
        $this->addVisit($p10->id, now()->subYears(10), 'Poli Umum', 'TBC', $petugas->id);


        // 3. GENERATE MASSAL (11 s.d 1253)
        $firstNames = ['Agus', 'Bambang', 'Candra', 'Dwi', 'Eko', 'Fajar', 'Guntur', 'Hendra', 'Indra', 'Joko', 'Kurniawan', 'Lucky', 'Mulyono', 'Nugroho', 'Oki', 'Prasetyo', 'Rully', 'Slamet', 'Teguh', 'Utomo', 'Wawan', 'Yanto', 'Zul', 'Siti', 'Dewi', 'Lestari', 'Putri', 'Sari', 'Indah', 'Rina', 'Wati', 'Maya', 'Eka', 'Ani', 'Bunga', 'Citra', 'Dian', 'Fitri', 'Gita', 'Hana'];
        $lastNames = ['Saputra', 'Wijaya', 'Kusuma', 'Hidayat', 'Santoso', 'Pratama', 'Sholeh', 'Gunawan', 'Setiawan', 'Ramadhan', 'Putra', 'Permana', 'Nugraha', 'Wibowo', 'Susanto', 'Purnama', 'Firmansyah', 'Ardiansyah'];
        $polis = ['Poli Umum', 'Poli Gigi', 'KIA/KB', 'IGD', 'Poli Lansia', 'Poli Anak'];
        $diagnosas = ['Febris', 'Hipertensi', 'Dyspepsia', 'Cephalgia', 'ISPA', 'Karies Gigi', 'Gastritis', 'Diabetes Melitus', 'Asma', 'Dermatitis'];

        $totalData = 1253;
        $batch = [];
        
        for ($i = 11; $i <= $totalData; $i++) {
            $formattedIndex = str_pad($i, 4, '0', STR_PAD_LEFT);
            $nama = $firstNames[array_rand($firstNames)] . " " . $lastNames[array_rand($lastNames)];
            $jk = (rand(0, 1) == 0 ? 'L' : 'P');
            
            // Random tanggal kunjungan antara hari ini s/d 10 tahun lalu (3650 hari)
            $randomDays = rand(0, 3650); 
            $tglKunjungan = now()->subDays($randomDays);
            
            $p = Patient::create([
                'no_rm' => "RM-$formattedIndex",
                'nik' => '35' . rand(10, 99) . rand(10, 99) . rand(10000000, 99999999),
                'nama_pasien' => $nama,
                'tgl_lahir' => now()->subYears(rand(18, 75))->subDays(rand(1, 365))->format('Y-m-d'),
                'jenis_kelamin' => $jk,
                'alamat_lengkap' => 'Jl. Sampel Data No. ' . rand(1, 300) . ', Kec. Arjasa, Jember',
            ]);

            $this->addVisit(
                $p->id, 
                $tglKunjungan, 
                $polis[array_rand($polis)], 
                $diagnosas[array_rand($diagnosas)], 
                $petugas->id
            );
        }
    }

    private function createData($rm, $nama, $jk, $tgl, $poli, $diag, $uid)
    {
        $p = Patient::create([
            'no_rm' => $rm,
            'nik' => '35' . rand(10, 99) . rand(10, 99) . rand(10000000, 99999999),
            'nama_pasien' => $nama,
            'tgl_lahir' => now()->subYears(rand(20, 50))->format('Y-m-d'),
            'jenis_kelamin' => $jk,
            'alamat_lengkap' => 'Alamat Pasien ' . $nama . ', Jember'
        ]);
        $this->addVisit($p->id, $tgl, $poli, $diag, $uid);
        return $p;
    }

    private function addVisit($pid, $tgl, $poli, $diag, $uid)
    {
        Visit::create([
            'no_registrasi' => 'REG-' . rand(100000, 999999),
            'patient_id' => $pid,
            'tgl_kunjungan' => $tgl,
            'poli_tujuan' => $poli,
            'dokter' => 'dr. ' . ['Suharyo', 'Mulyadi', 'Indah', 'Pratiwi', 'Bambang'][rand(0, 4)],
            'diagnosa' => $diag,
            'user_id' => $uid
        ]);
    }
}