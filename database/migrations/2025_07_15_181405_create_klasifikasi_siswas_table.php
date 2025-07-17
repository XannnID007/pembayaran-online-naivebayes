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
        Schema::create('klasifikasi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->enum('kategori_prediksi', ['pembayar_disiplin', 'pembayar_terlambat', 'pembayar_selektif']);
            $table->decimal('confidence_score', 5, 4)->nullable(); // 0.0000 - 1.0000
            $table->date('tanggal_prediksi');
            $table->json('detail_analisis')->nullable(); // menyimpan detail perhitungan
            $table->timestamps();

            // Index untuk performa
            $table->index(['siswa_id', 'tanggal_prediksi']);
            $table->index(['kategori_prediksi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klasifikasi_siswa');
    }
};
