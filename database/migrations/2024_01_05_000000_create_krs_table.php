<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->string('tahun_ajaran'); // ex: 2025/2026
            $table->enum('semester_ajaran', ['ganjil', 'genap'])->default('ganjil');
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'mata_kuliah_id', 'tahun_ajaran', 'semester_ajaran'], 'krs_unique_entry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};
