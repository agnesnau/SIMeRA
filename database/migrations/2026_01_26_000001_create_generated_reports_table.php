<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat')->unique();
            $table->enum('jenis_ba', ['retensi', 'pemusnahan']);
            $table->date('tanggal_ba');
            $table->integer('total_berkas');
            $table->string('dibuat_oleh'); 
            $table->json('payload_data'); // Menyimpan isi form (nama saksi, sk, dll)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};