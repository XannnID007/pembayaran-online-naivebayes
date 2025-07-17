<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisPembayaran;

class JenisPembayaranSeeder extends Seeder
{
    public function run()
    {
        $jenisPembayaran = [
            [
                'nama_pembayaran' => 'SPP',
                'nominal' => 500000,
                'periode' => 'bulanan',
                'deskripsi' => 'Sumbangan Pembinaan Pendidikan bulanan',
                'aktif' => true
            ],
            [
                'nama_pembayaran' => 'UTS',
                'nominal' => 200000,
                'periode' => 'semester',
                'deskripsi' => 'Ujian Tengah Semester',
                'aktif' => true
            ],
            [
                'nama_pembayaran' => 'UAS',
                'nominal' => 250000,
                'periode' => 'semester',
                'deskripsi' => 'Ujian Akhir Semester',
                'aktif' => true
            ]
        ];

        foreach ($jenisPembayaran as $jenis) {
            JenisPembayaran::create($jenis);
        }
    }
}
