<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    /**
     * Kolom yang boleh diisi secara massal.
     * manual_status ditambahkan agar bisa menyimpan status 'siap_musnah' atau 'dimusnahkan'
     */
    protected $fillable = [
        'no_rm', 
        'nama_pasien', 
        'nik', 
        'tgl_lahir', 
        'jenis_kelamin', 
        'alamat_lengkap', 
        'manual_status'
    ];

    protected $appends = ['current_status'];

    // Relasi ke riwayat kunjungan
    public function visits() {
        return $this->hasMany(Visit::class);
    }
    
    // Mengambil kunjungan paling baru saja
    public function lastVisit() {
        return $this->hasOne(Visit::class)->latestOfMany('tgl_kunjungan');
    }

    // Relasi ke catatan tindakan retensi (verifikasi fisik & upload)
    public function actions() {
        return $this->hasMany(RetentionAction::class);
    }

    /**
     * Logika Otomatisasi Status Retensi (SOP 2+2)
     * Menghasilkan label Aktif/Inaktif/Siap Musnah secara real-time
     */
    public function getCurrentStatusAttribute() {
        // 1. Cek status manual dulu (Prioritas Utama)
        if ($this->manual_status) {
            return match($this->manual_status) {
                'siap_musnah' => 'Siap Musnah',
                'dimusnahkan' => 'Dimusnahkan',
                default => 'Aktif'
            };
        }

        // 2. Hitung berdasarkan kunjungan terakhir
        $lastVisit = $this->lastVisit;
        if (!$lastVisit) return 'Inaktif'; 

        $yearsDiff = $lastVisit->tgl_kunjungan->diffInYears(now());

        if ($yearsDiff >= 4) {
            return 'Siap Musnah'; // Total 4 tahun (2 Aktif + 2 Inaktif)
        } elseif ($yearsDiff >= 2) {
            return 'Inaktif';     // Sudah 2 tahun tidak berkunjung
        } else {
            return 'Aktif';       // Masih dalam masa 2 tahun pelayanan
        }
    }
}