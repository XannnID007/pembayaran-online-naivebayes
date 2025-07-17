<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode',
        'bukti_transfer',
        'status_konfirmasi',
        'confirmed_by',
        'catatan'
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'decimal:2'
    ];

    // Relationships
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status_konfirmasi', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status_konfirmasi', 'confirmed');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match ($this->status_konfirmasi) {
            'pending' => '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Pending</span>',
            'confirmed' => '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Dikonfirmasi</span>',
            'rejected' => '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Ditolak</span>',
        };
    }
}
