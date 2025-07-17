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
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('jenis_pembayaran_id')->constrained('jenis_pembayaran')->onDelete('cascade');
            $table->string('bulan')->nullable(); // untuk SPP: 01, 02, dst
            $table->string('semester')->nullable(); // untuk UTS/UAS: ganjil/genap
            $table->year('tahun');
            $table->decimal('nominal', 15, 2);
            $table->date('deadline');
            $table->enum('status', ['belum_bayar', 'sudah_bayar'])->default('belum_bayar');
            $table->timestamps();

            // Index untuk performa
            $table->index(['siswa_id', 'status']);
            $table->index(['jenis_pembayaran_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
