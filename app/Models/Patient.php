<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Visit;

class Patient extends Model
{
    protected $guarded = [];
    protected $appends = ['current_status'];

    public function visits() {
        return $this->hasMany(Visit::class);
    }
    
    public function lastVisit() {
        return $this->hasOne(Visit::class)->latestOfMany('tgl_kunjungan');
    }

    /**
     * LOGIKA STATUS SESUAI KOREKSI SOP (2+2=4)
     * Aktif: < 2 Tahun
     * Inaktif: 2 s.d 4 Tahun
     * Siap Musnah: > 4 Tahun
     */
    public function getCurrentStatusAttribute() {
        // 1. Cek Manual Override (Status yang dipaksa oleh Petugas)
        if ($this->manual_status) {
            return match($this->manual_status) {
                'siap_musnah' => 'Siap Musnah',
                'dimusnahkan' => 'Dimusnahkan',
            };
        }

        // 2. Ambil Kunjungan Terakhir
        $lastVisit = $this->lastVisit;
        if (!$lastVisit) return 'Inaktif'; 

        $yearsDiff = $lastVisit->tgl_kunjungan->diffInYears(now());

        // Update Logika: 2 Tahun Aktif + 2 Tahun Inaktif = 4 Tahun Total
        if ($yearsDiff >= 4) {
            return 'Siap Musnah'; // Setelah total 4 tahun
        } elseif ($yearsDiff >= 2) {
            return 'Inaktif'; // Setelah 2 tahun aktif berakhir
        } else {
            return 'Aktif'; // Masih dalam masa 2 tahun kunjungan terakhir
        }
    }
}