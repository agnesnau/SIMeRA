<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\Visit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PatientsImport implements ToModel, WithHeadingRow
{
    /**
     * Logika: Sekali import langsung masuk ke data Pasien dan Riwayat Kunjungan.
     * Sekarang mencakup Reset Status Manual agar Algoritma Retensi sinkron.
     */
    public function model(array $row)
    {
        // 1. Validasi: Lewati baris jika No RM kosong
        if (!isset($row['no_rm']) || empty($row['no_rm'])) {
            return null;
        }

        // 2. Format No RM agar tetap 6 digit (misal: 009876)
        $formatted_no_rm = str_pad($row['no_rm'], 6, '0', STR_PAD_LEFT);

        /**
         * 3. Simpan atau Perbarui Data Pasien (updateOrCreate)
         * PENTING: manual_status di-set ke NULL agar jika pasien ini dulunya "Siap Musnah",
         * statusnya kembali mengikuti perhitungan tanggal kunjungan terbaru (Aktif kembali).
         */
        $patient = Patient::updateOrCreate(
            ['no_rm' => $formatted_no_rm], 
            [
                'nik'            => $row['nik'] ?? null,
                'nama_pasien'    => $row['nama_pasien'] ?? $row['nama'] ?? '-',
                'tgl_lahir'      => $this->transformDate($row['tgl_lahir'] ?? null),
                'jenis_kelamin'  => strtoupper($row['jenis_kelamin'] ?? 'L'),
                'alamat_lengkap' => $row['alamat_lengkap'] ?? '-',
                'manual_status'  => null, // <-- DB DI UPDATE DI SINI: Reset status retensi manual
            ]
        );

        /**
         * 4. Catat Riwayat Kunjungan Baru dengan Detail Medis Lengkap
         * Menghasilkan baris baru di tabel 'visits' yang otomatis mengupdate 'lastVisit'.
         */
        return new Visit([
            'no_registrasi' => 'IMP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
            'patient_id'    => $patient->id,
            'tgl_kunjungan' => $this->transformDate($row['tgl_kunjungan_terakhir'] ?? null),
            
            // Logika Medis Dinamis
            'dokter'        => $row['nama_dokter'] ?? 'Dokter Migrasi',
            'poli_tujuan'   => $row['poli_tujuan'] ?? 'Umum',
            'diagnosa'      => $row['diagnosa_terakhir'] ?? 'Data Migrasi Awal',
            
            'user_id'       => Auth::id(), // Petugas pelaksana import
        ]);
    }

    /**
     * Helper: Mengubah format tanggal Excel (Serial/String) menjadi format database.
     */
    private function transformDate($value)
    {
        if (empty($value)) return Carbon::now();

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value);
            }
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }
}