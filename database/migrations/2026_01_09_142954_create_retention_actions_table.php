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
        Schema::create('retention_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained();
            $table->foreignId('user_id')->constrained(); // Siapa yang melakukan aksi
            $table->enum('action_type', ['verifikasi_fisik', 'ajukan_musnah', 'eksekusi_musnah', 'restore']);
            $table->text('keterangan')->nullable(); // Alasan/Catatan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retention_actions');
    }
};
