<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $guarded = []; // Izinkan semua kolom diisi

    // PENTING: Casting agar tgl_kunjungan dianggap sebagai objek Tanggal (Carbon)
    protected $casts = [
        'tgl_kunjungan' => 'date',
    ];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}