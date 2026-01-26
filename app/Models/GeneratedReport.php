<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedReport extends Model
{
    protected $table = 'generated_reports';
    protected $fillable = ['no_surat', 'jenis_ba', 'tanggal_ba', 'total_berkas', 'dibuat_oleh', 'payload_data'];

    // Casting JSON agar otomatis menjadi array saat dipanggil di Controller/View
    protected $casts = [
        'payload_data' => 'array',
        'tanggal_ba' => 'date'
    ];
}