<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'kelas',
        'alamat',
        'no_hp',
        'status'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function klasifikasi()
    {
        return $this->hasMany(KlasifikasiSiswa::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Accessors
    public function getTotalTagihanAttribute()
    {
        return $this->tagihan()->where('status', 'belum_bayar')->sum('nominal');
    }

    public function getTagihanTerbayarAttribute()
    {
        return $this->tagihan()->where('status', 'sudah_bayar')->sum('nominal');
    }
}
