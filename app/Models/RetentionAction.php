<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetentionAction extends Model
{
    // Membuka kunci agar semua kolom bisa diisi oleh Seeder/Controller
    protected $guarded = []; 

    // Relasi ke User
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Pasien
    public function patient() {
        return $this->belongsTo(Patient::class);
    }
}