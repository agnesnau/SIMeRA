<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'no_rm', 
        'nama_pasien', 
        'nik', 
        'tgl_lahir', 
        'jenis_kelamin', 
        'alamat_lengkap', 
        'manual_status', 
        'status_approval'
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
     * Logika Otomatisasi Status Retensi (SOP 2+5)
     * Menggunakan LOGIKA TANGGAL KETAT (subYears) agar hitungan hari akurat.
     */
    public function getCurrentStatusAttribute() {
        // 1. Cek status manual dulu (Prioritas Utama)
        // Jika ada status manual, kembalikan status tersebut agar konsisten
        if ($this->manual_status) {
            return match($this->manual_status) {
                'dimusnahkan' => 'Dimusnahkan',
                'siap_musnah' => 'Siap Musnah',
                'digudang'    => 'Inaktif', // Dianggap Inaktif karena sudah di gudang
                'pemilahan'   => 'Inaktif', // Sedang dipilah berarti masuk masa inaktif
                default       => 'Aktif'
            };
        }

        // 2. Hitung berdasarkan kunjungan terakhir
        $lastVisit = $this->lastVisit;
        
        // Jika tidak ada kunjungan, anggap Aktif (Pasien Baru)
        if (!$lastVisit) return 'Aktif'; 

        $tglKunjungan = Carbon::parse($lastVisit->tgl_kunjungan);
        
        // Tentukan Batas Waktu Mundur dari Hari Ini
        $batasInaktif = now()->subYears(2); // Contoh: Hari ini 2026, batasnya 2024
        $batasMusnah  = now()->subYears(5); // Contoh: Hari ini 2026, batasnya 2021

        // 3. Logika Perbandingan Tanggal
        if ($tglKunjungan->lessThanOrEqualTo($batasMusnah)) {
            // Jika kunjungan SEBELUM tahun 2021 (Sudah > 5 Tahun)
            return 'Siap Musnah';
        } 
        elseif ($tglKunjungan->lessThanOrEqualTo($batasInaktif)) {
            // Jika kunjungan SEBELUM tahun 2024 (Sudah > 2 Tahun)
            return 'Inaktif';
        } 
        else {
            // Jika kunjungan SETELAH tahun 2024 (Masih Baru)
            return 'Aktif';
        }
    }
}