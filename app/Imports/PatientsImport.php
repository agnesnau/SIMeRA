<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\Visit;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date; // Tambahkan ini di bagian atas

class PatientsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Format No RM agar tetap 6 digit (009876)
        $formatted_no_rm = str_pad($row['no_rm'], 6, '0', STR_PAD_LEFT);

        // 2. Logika konversi tanggal agar tidak jadi 1970
        $tanggal_input = $row['tgl_kunjungan_terakhir'] ?? null;
        
        if ($tanggal_input) {
            // Jika user menulis format angka di Excel (Excel Serial Date)
            if (is_numeric($tanggal_input)) {
                $tgl_kunjungan = Carbon::instance(Date::excelToDateTimeObject($tanggal_input))->format('Y-m-d');
            } else {
                // Jika user menulis teks (misal: 2025-10-20)
                $tgl_kunjungan = Carbon::parse($tanggal_input)->format('Y-m-d');
            }
        } else {
            $tgl_kunjungan = date('Y-m-d'); // Default hari ini jika kosong
        }

        // 3. Simpan Pasien
        $patient = Patient::updateOrCreate(
            ['no_rm' => $formatted_no_rm],
            [
                'nama_pasien' => $row['nama_pasien'] ?? $row['nama'] ?? '-',
                'nik'         => $row['nik'] ?? null,
                'tgl_lahir'   => '1900-01-01', // Nilai default agar tidak error SQL
            ]
        );

        // 4. Catat Kunjungan dengan tanggal yang benar
        Visit::create([
            'no_registrasi' => 'IMP-' . strtoupper(bin2hex(random_bytes(2))),
            'patient_id'    => $patient->id,
            'tgl_kunjungan' => $tgl_kunjungan, // Menggunakan hasil konversi di atas
            'poli_tujuan'   => 'Umum',
            'dokter'        => 'Dokter Import',
            'diagnosa'      => 'Import Data Awal'
        ]);

        return $patient;
    }
}