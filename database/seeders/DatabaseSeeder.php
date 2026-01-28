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
     * Jalankan database seeder.
     * Fitur: 3 User + 25 Pasien Realistik (Nama Standar RME, Dokter & Diagnosa Sesuai Poli).
     */
    public function run(): void
    {
        // ==========================================
        // 1. SETUP USER (ADMIN, PETUGAS, KAPUS)
        // ==========================================
        
        // Admin
        User::updateOrCreate(['username' => 'admin'], [
            'nip' => '198501012010011001',
            'nama_lengkap' => 'Administrator Sistem',
            'password' => Hash::make('123'),
            'level' => 'admin'
        ]);

        // Petugas
        User::updateOrCreate(['username' => 'petugas'], [
            'nip' => '199002022015022002',
            'nama_lengkap' => 'Petugas Rekam Medis',
            'password' => Hash::make('123'),
            'level' => 'petugas'
        ]);

        // Kapuskesmas
        User::updateOrCreate(['username' => 'kapuskesmas'], [
            'nip' => '197503032005011005',
            'nama_lengkap' => 'Dr. Kepala Puskesmas',
            'password' => Hash::make('123'),
            'level' => 'supervisor'
        ]);

        // Ambil ID Petugas untuk data kunjungan
        $petugasId = User::where('username', 'petugas')->first()->id;

        // ==========================================
        // 2. DATA REFERENSI
        // ==========================================

        $alamatDesa = [
            'Dsn. Krajan, Desa Sempolan', 
            'Dsn. Pasar Alas, Desa Garahan', 
            'Dsn. Curah Damar, Desa Sidomulyo', 
            'Dsn. Sepuran, Desa Sumberjati'
        ];

        $daftarPasien = [
            ['nama' => 'Sutrisno', 'jk' => 'L'], 
            ['nama' => 'Poniyem', 'jk' => 'P'],
            ['nama' => 'Wagiman', 'jk' => 'L'], 
            ['nama' => 'Suparman', 'jk' => 'L'],
            ['nama' => 'Ngatmini', 'jk' => 'P'], 
            ['nama' => 'Slamet Riadi', 'jk' => 'L'],
            ['nama' => 'Tumini', 'jk' => 'P'], 
            ['nama' => 'Karto', 'jk' => 'L'],
            ['nama' => 'Suti', 'jk' => 'P'], 
            ['nama' => 'Joko Susilo', 'jk' => 'L'],
            ['nama' => 'Sri Wahyuni', 'jk' => 'P'], 
            ['nama' => 'Surip', 'jk' => 'L'],
            ['nama' => 'Siti Aminah', 'jk' => 'P'], 
            ['nama' => 'Bambang Sugeng', 'jk' => 'L'],
            ['nama' => 'Wati', 'jk' => 'P'], 
            ['nama' => 'Tarni', 'jk' => 'L'],
            ['nama' => 'Rumini', 'jk' => 'P'], 
            ['nama' => 'Agus Santoso', 'jk' => 'L'],
            ['nama' => 'Lilik Suryani', 'jk' => 'P'], 
            ['nama' => 'Darmo', 'jk' => 'L'],
            ['nama' => 'Yuniarsih', 'jk' => 'P'], 
            ['nama' => 'Saleh', 'jk' => 'L'],
            ['nama' => 'Misri', 'jk' => 'P'], 
            ['nama' => 'Sugiono', 'jk' => 'L'],
            ['nama' => 'Lasmini', 'jk' => 'P'],
        ];

        // ==========================================
        // 3. PROSES GENERATE DATA
        // ==========================================

        $counter = 1;

        foreach ($daftarPasien as $data) {
            // A. GENERATE PASIEN
            // NIK Jember: 3509...
            $nik = '3509' . rand(10, 33) . rand(10, 33) . rand(40, 70) . '000' . rand(1, 9);
            // No RM urut
            $noRM = '143509110005-' . str_pad($counter, 6, '0', STR_PAD_LEFT);
            // Alamat random dari 4 desa
            $desa = $alamatDesa[array_rand($alamatDesa)];
            $alamatLengkap = "$desa, RT " . rand(1, 15) . " RW " . rand(1, 5) . ", Kec. Silo, Kab. Jember";
            // Tgl Lahir (Umur 20-80 tahun)
            $tglLahir = Carbon::now()->subYears(rand(20, 80))->subDays(rand(0, 360))->format('Y-m-d');

            $patient = Patient::firstOrCreate(
                ['no_rm' => $noRM],
                [
                    'nik' => $nik,
                    'nama_pasien' => $data['nama'],
                    'jenis_kelamin' => $data['jk'],
                    'tgl_lahir' => $tglLahir,
                    'alamat_lengkap' => $alamatLengkap
                ]
            );

            // B. GENERATE KUNJUNGAN (LOGIKA DOKTER & POLI)
            // Tahun Kunjungan: 2016 - 2025
            $tahun = rand(2016, 2025);
            $tglKunjungan = Carbon::create($tahun, rand(1, 12), rand(1, 28))->format('Y-m-d');

            // Logika Poli
            // 1 = Umum, 2 = Gigi, 3 = KIA
            $pilihan = rand(1, 3);
            
            // Jika Laki-laki, paksa ke Umum atau Gigi (Jangan KIA)
            if ($data['jk'] == 'L' && $pilihan == 3) {
                $pilihan = rand(1, 2); 
            }

            if ($pilihan == 1) {
                $poli = 'Poli Umum';
                $dokter = 'dr. Adi Wijaya SE';
                $diagnosa = ['Febris', 'Hipertensi', 'ISPA', 'Cephalgia', 'Myalgia', 'Gastritis', 'Diabetes Melitus'][rand(0, 6)];
            } elseif ($pilihan == 2) {
                $poli = 'Poli Gigi';
                $dokter = 'drg Luk Luk Nurhayati';
                $diagnosa = ['Pulpitis', 'Gingivitis', 'Karies Gigi', 'Abses Gigi', 'Periodontitis', 'Sakit Gigi', 'Cabut Gigi'][rand(0, 6)];
            } else {
                $poli = 'KIA';
                $dokter = 'Bidan Fitri Yuni Amd.keb';
                $diagnosa = ['Pemeriksaan Kehamilan', 'Imunisasi TT', 'KB Suntik', 'KB Pil', 'Ibu Hamil KEK', 'Kontrol Nifas'][rand(0, 5)];
            }

            // Simpan Kunjungan
            Visit::create([
                'patient_id' => $patient->id,
                'no_registrasi' => 'REG-' . str_replace('-', '', $tglKunjungan) . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                'tgl_kunjungan' => $tglKunjungan,
                'poli_tujuan' => $poli,
                'dokter' => $dokter,
                'diagnosa' => $diagnosa,
                'user_id' => $petugasId
            ]);

            $counter++;
        }
    }
}