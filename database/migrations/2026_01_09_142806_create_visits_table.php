<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('no_registrasi', 20)->unique(); // Contoh: REG-20260101-001
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->date('tgl_kunjungan');
            $table->string('poli_tujuan', 50); // Contoh: Poli Penyakit Dalam
            $table->string('dokter', 100)->nullable(); // Nama Dokter Pemeriksa
            $table->text('diagnosa'); // Diagnosa Utama (ICD-10 atau deskripsi)
            $table->foreignId('user_id')->nullable()->constrained('users'); // Petugas pendaftaran
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
