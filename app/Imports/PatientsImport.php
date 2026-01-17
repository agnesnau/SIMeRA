<?php
namespace App\Imports;

use App\Models\Patient;
use App\Models\Visit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PatientsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Pastikan nama kolom di Excel (heading) sesuai: no_rm, nama, tgl_lahir, tgl_kunjungan_terakhir
        $patient = Patient::updateOrCreate(
            ['no_rm' => $row['no_rm']],
            [
                'nama_pasien' => $row['nama'],
                'tgl_lahir'   => $row['tgl_lahir'], // Format YYYY-MM-DD
                'jenis_kelamin' => $row['jk'],
                'alamat_lengkap' => $row['alamat']
            ]
        );

        // Auto input kunjungan terakhir agar status retensi valid
        if(isset($row['tgl_kunjungan_terakhir'])) {
            Visit::create([
                'no_registrasi' => 'IMP-' . rand(1000,9999),
                'patient_id' => $patient->id,
                'tgl_kunjungan' => $row['tgl_kunjungan_terakhir'],
                'poli_tujuan' => 'Umum',
                'diagnosa' => 'Import Data'
            ]);
        }

        return $patient;
    }
}