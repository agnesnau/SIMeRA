<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('patients', function (Blueprint $table) {
        // Kita ubah jadi string (varchar) biar fleksibel menampung 'pemilahan', 'siap_musnah', dll
        $table->string('manual_status', 50)->nullable()->change();
    });
}
};
