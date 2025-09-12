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
        $sppId = $jenisPembayaran->where('nama_pembayaran', 'SPP')->first()->id;
        $utsId = $jenisPembayaran->where('nama_pembayaran', 'UTS')->first()->id;
        $uasId = $jenisPembayaran->where('nama_pembayaran', 'UAS')->first()->id;

        foreach ($siswa as $index => $s) {
            // Create different payment patterns for different students
            $pattern = ($index % 3) + 1;

            // Generate SPP untuk 8 bulan terakhir dengan pola berbeda
            for ($i = 7; $i >= 0; $i--) {
                $bulan = Carbon::now()->subMonths($i);

                // Tentukan status berdasarkan pola siswa
                $status = $this->determinePaymentStatus($pattern, $i);

                Tagihan::create([
                    'siswa_id' => $s->id,
                    'jenis_pembayaran_id' => $sppId,
                    'bulan' => $bulan->format('m'),
                    'tahun' => $bulan->year,
                    'nominal' => 500000,
                    'deadline' => $bulan->endOfMonth(),
                    'status' => $status,
                ]);
            }

            // Generate UTS dengan pola berbeda
            $this->generateSemesterTagihan($s->id, $utsId, 'UTS', $pattern);

            // Generate UAS dengan pola berbeda
            $this->generateSemesterTagihan($s->id, $uasId, 'UAS', $pattern);
        }
    }

    private function determinePaymentStatus($pattern, $monthsAgo)
    {
        switch ($pattern) {
            case 1: // Disiplin - bayar hampir semua
                return ($monthsAgo <= 1) ? 'belum_bayar' : 'sudah_bayar';

            case 2: // Selektif - bayar sebagian
                return ($monthsAgo <= 3 || $monthsAgo === 6) ? 'belum_bayar' : 'sudah_bayar';

            case 3: // Terlambat - bayar sedikit
                return ($monthsAgo <= 5) ? 'belum_bayar' : 'sudah_bayar';

            default:
                return 'belum_bayar';
        }
    }

    private function generateSemesterTagihan($siswaId, $jenisPembayaranId, $jenis, $pattern)
    {
        // UTS Ganjil 2024/2025
        $status = $this->getSemesterPaymentStatus($pattern, $jenis, 'ganjil');
        Tagihan::create([
            'siswa_id' => $siswaId,
            'jenis_pembayaran_id' => $jenisPembayaranId,
            'semester' => 'ganjil',
            'tahun' => 2025,
            'nominal' => $jenis === 'UTS' ? 200000 : 250000,
            'deadline' => $jenis === 'UTS' ?
                Carbon::create(2024, 10, 15) :
                Carbon::create(2024, 12, 20),
            'status' => $status,
        ]);

        // UTS/UAS Genap 2024/2025
        $status = $this->getSemesterPaymentStatus($pattern, $jenis, 'genap');
        Tagihan::create([
            'siswa_id' => $siswaId,
            'jenis_pembayaran_id' => $jenisPembayaranId,
            'semester' => 'genap',
            'tahun' => 2025,
            'nominal' => $jenis === 'UTS' ? 200000 : 250000,
            'deadline' => $jenis === 'UTS' ?
                Carbon::create(2025, 4, 15) :
                Carbon::create(2025, 6, 20),
            'status' => $status,
        ]);
    }

    private function getSemesterPaymentStatus($pattern, $jenis, $semester)
    {
        switch ($pattern) {
            case 1: // Disiplin - bayar semua jenis
                return ($semester === 'genap' && $jenis === 'UAS') ? 'belum_bayar' : 'sudah_bayar';

            case 2: // Selektif - hanya bayar jenis tertentu
                if ($jenis === 'UTS') {
                    return 'sudah_bayar'; // Selektif biasanya bayar UTS saja
                } else {
                    return ($semester === 'ganjil') ? 'sudah_bayar' : 'belum_bayar';
                }

            case 3: // Terlambat - bayar sedikit
                return ($jenis === 'UTS' && $semester === 'ganjil') ? 'sudah_bayar' : 'belum_bayar';

            default:
                return 'belum_bayar';
        }
    }
}
