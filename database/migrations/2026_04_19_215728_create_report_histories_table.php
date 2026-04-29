<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_histories', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->date('tanggal_ba');
            $table->string('jenis_ba');
            $table->string('tipe_dokumen');
            $table->json('data_json')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_histories');
    }
};