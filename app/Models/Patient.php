<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Visit;
use App\Models\RetentionAction; // Import model action

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

    // TAMBAHKAN RELASI INI: Agar bisa cek apakah pasien sudah di-upload nilainya
    public function actions() {
        return $this->hasMany(RetentionAction::class);
    }

    public function getCurrentStatusAttribute() {
        if ($this->manual_status) {
            return match($this->manual_status) {
                'siap_musnah' => 'Siap Musnah',
                'dimusnahkan' => 'Dimusnahkan',
            };
        }

        $lastVisit = $this->lastVisit;
        if (!$lastVisit) return 'Inaktif'; 

        $yearsDiff = $lastVisit->tgl_kunjungan->diffInYears(now());

        if ($yearsDiff >= 4) {
            return 'Siap Musnah'; 
        } elseif ($yearsDiff >= 2) {
            return 'Inaktif'; 
        } else {
            return 'Aktif'; 
        }
    }
}