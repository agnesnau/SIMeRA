<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Patient;
use App\Models\Visit;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan database seeder tanpa menghapus data lama.
     * Menggunakan updateOrCreate dan firstOrCreate agar aman dijalankan berulang kali.
     */
    public function run(): void
    {
        // 1. DATA PENGGUNA (Update jika sudah ada username yang sama)
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nip' => '198501012010011001',
                'nama_lengkap' => 'Administrator Sistem',
                'password' => Hash::make('123'),
                'level' => 'admin'
            ]
        );

        User::updateOrCreate(
            ['username' => 'petugas'],
            [
                'nip' => '199002022015022002',
                'nama_lengkap' => 'Petugas Rekam Medis',
                'password' => Hash::make('123'),
                'level' => 'petugas'
            ]
        );

        // Ambil ID petugas untuk relasi kunjungan
        $petugasId = User::where('username', 'petugas')->first()->id;

        // 2. DATA SAMPEL NYATA (Menggunakan format no_index baru)
        $realData = [
            ['143509110005-008836', 'SUMO', 'L', '2025-12-01'],
            ['143509110005-000038', 'MISRI', 'P', '2025-12-01'],
            ['143509110005-035390', 'SUBEWI', 'L', '2025-12-01'],
            ['143509110005-001105', 'FITI YUGI WARNI', 'P', '2025-12-01'],
            ['143509110005-036589', 'MAHIRHOTUL AULAWIYAH', 'P', '2025-12-01'],
            ['143509110005-036590', 'P. NAWIR', 'L', '2025-12-01'],
            ['143509110005-036948', 'SUSILA WATI', 'P', '2025-12-29'],
            ['143509110005-036761', 'USWATUL HASANAH', 'P', '2025-12-29'],
            ['143509110005-031774', 'MAIMUNA', 'P', '2025-12-29'],
            ['143509110005-034973', 'IQLILAH DINI FAJRIATI', 'P', '2025-12-29'],
            ['143509110005-036952', 'BY NY IZA KHOFIFAH', 'L', '2025-12-29'],
        ];

        $counter = 1;
        foreach ($realData as $data) {
            $this->createData($data[0], $data[1], $data[2], $data[3], $petugasId, $counter++);
        }

        // 3. DATA DUMMY TAMBAHAN (Hingga total 100 data)
        $totalTarget = 100;
        
        $names = ['Slamet', 'Budi', 'Siti', 'Dewi', 'Agus', 'Rina', 'Joko', 'Eko', 'Yanti', 'Wati'];
        $surnames = ['Santoso', 'Wijaya', 'Kusuma', 'Hidayat', 'Pratama', 'Gunawan', 'Setiawan'];

        for ($i = $counter; $i <= $totalTarget; $i++) {
            $formattedIndex = "143509110005-" . str_pad($i, 6, '0', STR_PAD_LEFT);
            $nama = $names[array_rand($names)] . " " . $surnames[array_rand($surnames)];
            $jk = (rand(0, 1) == 0 ? 'L' : 'P');
            $randomDate = now()->subDays(rand(0, 1800))->format('Y-m-d');
            
            $this->createData($formattedIndex, $nama, $jk, $randomDate, $petugasId, $i);
        }
    }

    /**
     * Helper untuk membuat data Pasien dan Kunjungan secara cerdas.
     */
    private function createData($index, $nama, $jk, $tglKunjungan, $uid, $uniqueId)
    {
        // Mencari atau membuat pasien baru berdasarkan No Index (RM)
        $p = Patient::firstOrCreate(
            ['no_rm' => $index],
            [
                'nik' => '3509' . str_pad($uniqueId, 12, '0', STR_PAD_LEFT),
                'nama_pasien' => $nama,
                'tgl_lahir' => now()->subYears(rand(20, 60))->format('Y-m-d'),
                'jenis_kelamin' => $jk,
                'alamat_lengkap' => 'Dsn. Contoh Data No. ' . $uniqueId . ', Jember'
            ]
        );

        // Update atau buat data kunjungan berdasarkan Nomor Registrasi agar tidak error Duplicate
        Visit::updateOrCreate(
            ['no_registrasi' => 'REG-' . str_pad($uniqueId, 8, '0', STR_PAD_LEFT)],
            [
                'patient_id' => $p->id,
                'tgl_kunjungan' => $tglKunjungan,
                'dokter' => 'dr. ' . ['Suharyo', 'Mulyadi', 'Indah', 'Pratiwi'][rand(0, 3)],
                'user_id' => $uid,
                'poli_tujuan' => ['Poli Umum', 'Poli Gigi', 'KIA', 'IGD'][rand(0, 3)],
                'diagnosa' => ['Febris', 'Hipertensi', 'Dyspepsia', 'Cephalgia', 'ISPA'][rand(0, 4)]
            ]
        );
    }
}