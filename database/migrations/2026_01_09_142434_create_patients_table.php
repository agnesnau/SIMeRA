<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('patients', function (Blueprint $table) {
        $table->id();
        $table->string('no_rm')->unique();
        $table->string('nik', 16)->nullable()->unique();
        $table->string('no_bpjs', 20)->nullable();
        $table->string('nama_pasien');
        $table->string('tempat_lahir')->nullable();
        $table->date('tgl_lahir');
        $table->enum('jenis_kelamin', ['L', 'P']);
        
        // PASTIKAN INI ADALAH 'alamat_lengkap' (Bukan 'address' atau 'alamat')
        $table->text('alamat_lengkap')->nullable(); 
        
        $table->string('no_hp', 15)->nullable();
        $table->string('pekerjaan')->nullable();
        $table->enum('manual_status', ['siap_musnah', 'dimusnahkan'])->nullable(); 
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};