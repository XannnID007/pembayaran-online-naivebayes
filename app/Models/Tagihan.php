<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'siswa_id',
        'jenis_pembayaran_id',
        'bulan',
        'semester',
        'tahun',
        'nominal',
        'deadline',
        'status'
    ];

    protected $casts = [
        'deadline' => 'date',
        'nominal' => 'decimal:2'
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // Scopes
    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    public function scopeSudahBayar($query)
    {
        return $query->where('status', 'sudah_bayar');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'belum_bayar')
            ->where('deadline', '<', Carbon::now());
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'belum_bayar' => $this->deadline < Carbon::now() ?
                '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Terlambat</span>' :
                '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Belum Bayar</span>',
            'sudah_bayar' => '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Sudah Bayar</span>',
        };
    }

    public function getIsTerlambatAttribute()
    {
        return $this->status === 'belum_bayar' && $this->deadline < Carbon::now();
    }
}
