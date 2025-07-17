<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\JenisPembayaran;
use Carbon\Carbon;

class TagihanSeeder extends Seeder
{
    public function run()
    {
        $siswa = Siswa::all();
        $jenisPembayaran = JenisPembayaran::all();

        foreach ($siswa as $s) {
            // Generate SPP untuk 6 bulan terakhir
            for ($i = 5; $i >= 0; $i--) {
                $bulan = Carbon::now()->subMonths($i);

                Tagihan::create([
                    'siswa_id' => $s->id,
                    'jenis_pembayaran_id' => $jenisPembayaran->where('nama_pembayaran', 'SPP')->first()->id,
                    'bulan' => $bulan->format('m'),
                    'tahun' => $bulan->year,
                    'nominal' => 500000,
                    'deadline' => $bulan->endOfMonth(),
                    'status' => $i >= 3 ? 'sudah_bayar' : 'belum_bayar', // 3 bulan terakhir belum bayar
                ]);
            }

            // Generate UTS Ganjil 2024/2025
            Tagihan::create([
                'siswa_id' => $s->id,
                'jenis_pembayaran_id' => $jenisPembayaran->where('nama_pembayaran', 'UTS')->first()->id,
                'semester' => 'ganjil',
                'tahun' => 2025,
                'nominal' => 200000,
                'deadline' => Carbon::create(2024, 10, 15),
                'status' => 'sudah_bayar',
            ]);

            // Generate UAS Ganjil 2024/2025
            Tagihan::create([
                'siswa_id' => $s->id,
                'jenis_pembayaran_id' => $jenisPembayaran->where('nama_pembayaran', 'UAS')->first()->id,
                'semester' => 'ganjil',
                'tahun' => 2025,
                'nominal' => 250000,
                'deadline' => Carbon::create(2024, 12, 20),
                'status' => rand(0, 1) ? 'sudah_bayar' : 'belum_bayar',
            ]);
        }
    }
}
