<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    protected $fillable = [
        'no_rm', 
        'nama_pasien', 
        'nik', 
        'tgl_lahir', 
        'jenis_kelamin', 
        'alamat_lengkap', 
        'manual_status', 
        'status_approval',
        'nilai_guna_path'
    ];

    protected $appends = ['current_status'];

    public function visits() {
        return $this->hasMany(Visit::class);
    }
    
    public function lastVisit() {
        return $this->hasOne(Visit::class)->latestOfMany('tgl_kunjungan');
    }

    public function actions() {
        return $this->hasMany(RetentionAction::class);
    }

    /**
     * Logika Otomatisasi Status Retensi (SOP 2+3 = 5 Tahun)
     */
    public function getCurrentStatusAttribute() {
        // 1. CEK STATUS MANUAL DENGAN AMAN
        // Gunakan trim() untuk memastikan spasi kosong (" ") tidak dianggap sebagai status
        if (!empty(trim($this->manual_status))) {
            $ms = strtolower(trim($this->manual_status));
            
            // Hanya cocokkan status yang valid. Jika tidak valid, biarkan lolos ke perhitungan tanggal.
            if ($ms === 'dimusnahkan') return 'Dimusnahkan';
            if ($ms === 'siap_musnah') return 'Siap Musnah';
            if ($ms === 'digudang') return 'Di Gudang'; // Pisahkan penamaan agar mudah difilter
            if ($ms === 'pemilahan') return 'Pemilahan';
        }

        // 2. HITUNG BERDASARKAN KUNJUNGAN TERAKHIR
        $lastVisit = $this->lastVisit;
        
        // Jika benar-benar tidak ada kunjungan, anggap Aktif (Pasien Baru)
        if (!$lastVisit) return 'Aktif'; 

        $tglKunjungan = Carbon::parse($lastVisit->tgl_kunjungan);
        
        $batasInaktif = now()->subYears(2); 
        $batasMusnah  = now()->subYears(5); 

        // 3. LOGIKA TANGGAL KETAT
        if ($tglKunjungan->lessThanOrEqualTo($batasMusnah)) {
            // Jika Kunjungan <= 2021 (Sudah 5 Tahun atau lebih)
            return 'Siap Musnah';
        } 
        elseif ($tglKunjungan->lessThanOrEqualTo($batasInaktif)) {
            // Jika Kunjungan <= 2024 (Sudah 2 Tahun, tapi belum 5 tahun)
            return 'Inaktif';
        } 
        else {
            // Jika Kunjungan masih baru
            return 'Aktif';
        }
    }
}