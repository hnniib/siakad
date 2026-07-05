<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Dipakai khusus untuk jenis 'ujian_uts' / 'ujian_uas' (tanggal kalender asli).
            // Untuk jenis 'kuliah' kolom ini tetap null karena kuliah berpola mingguan (pakai kolom 'hari').
            $table->date('tanggal')->nullable()->after('hari');
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};