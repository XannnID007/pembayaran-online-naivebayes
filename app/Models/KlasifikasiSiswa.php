<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlasifikasiSiswa extends Model
{
    use HasFactory;

    protected $table = 'klasifikasi_siswa';

    protected $fillable = [
        'siswa_id',
        'kategori_prediksi',
        'confidence_score',
        'tanggal_prediksi',
        'detail_analisis'
    ];

    protected $casts = [
        'tanggal_prediksi' => 'date',
        'confidence_score' => 'decimal:4',
        'detail_analisis' => 'array'
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Accessors
    public function getKategoriBadgeAttribute()
    {
        return match ($this->kategori_prediksi) {
            'pembayar_disiplin' => '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Disiplin</span>',
            'pembayar_terlambat' => '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Terlambat</span>',
            'pembayar_selektif' => '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Selektif</span>',
        };
    }

    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori_prediksi) {
            'pembayar_disiplin' => 'Pembayar Disiplin',
            'pembayar_terlambat' => 'Pembayar Terlambat',
            'pembayar_selektif' => 'Pembayar Selektif',
        };
    }
}
