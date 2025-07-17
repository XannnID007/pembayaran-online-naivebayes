<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    use HasFactory;

    protected $table = 'jenis_pembayaran';

    protected $fillable = [
        'nama_pembayaran',
        'nominal',
        'periode',
        'deskripsi',
        'aktif'
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'nominal' => 'decimal:2'
    ];

    // Relationships
    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
