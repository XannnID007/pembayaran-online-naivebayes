<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    public function run()
    {
        $tagihan = Tagihan::where('status', 'sudah_bayar')->get();

        foreach ($tagihan as $t) {
            // Create realistic payment patterns based on student behavior
            $siswaId = $t->siswa_id;

            // Define different payment patterns based on student ID for variety
            $pattern = ($siswaId % 3) + 1;

            switch ($pattern) {
                case 1: // Disiplin - bayar awal
                    $tanggalBayar = $t->deadline->subDays(rand(10, 20));
                    break;
                case 2: // Selektif - bayar mendekati deadline
                    $tanggalBayar = $t->deadline->subDays(rand(1, 5));
                    break;
                case 3: // Terlambat - bayar setelah deadline
                    $tanggalBayar = $t->deadline->addDays(rand(1, 10));
                    break;
            }

            // Ensure payment date is not in the future
            if ($tanggalBayar->isFuture()) {
                $tanggalBayar = Carbon::now()->subDays(rand(1, 30));
            }

            Pembayaran::create([
                'tagihan_id' => $t->id,
                'tanggal_bayar' => $tanggalBayar,
                'jumlah_bayar' => $t->nominal,
                'metode' => rand(0, 1) ? 'tunai' : 'transfer',
                'bukti_transfer' => rand(0, 1) ? 'bukti_' . $t->id . '.jpg' : null,
                'status_konfirmasi' => 'confirmed',
                'confirmed_by' => 1, // Admin user ID
                'catatan' => 'Pembayaran ' . $t->jenisPembayaran->nama_pembayaran
            ]);
        }

        // Add some additional payments for better training data
        $this->createVariedPaymentPatterns();
    }

    private function createVariedPaymentPatterns()
    {
        $tagihan = Tagihan::where('status', 'belum_bayar')->take(10)->get();

        foreach ($tagihan as $index => $t) {
            // Create different payment behaviors
            if ($index % 4 === 0) {
                // Very disciplined - pays very early
                $tanggalBayar = $t->deadline->subDays(rand(15, 25));
                if (!$tanggalBayar->isFuture()) {
                    $this->createPayment($t, $tanggalBayar);
                }
            } elseif ($index % 4 === 1) {
                // Moderately disciplined - pays on time
                $tanggalBayar = $t->deadline->subDays(rand(3, 7));
                if (!$tanggalBayar->isFuture()) {
                    $this->createPayment($t, $tanggalBayar);
                }
            } elseif ($index % 4 === 2) {
                // Selective - pays some bills late
                if (rand(0, 1)) {
                    $tanggalBayar = $t->deadline->addDays(rand(3, 8));
                    if (!$tanggalBayar->isFuture()) {
                        $this->createPayment($t, $tanggalBayar);
                    }
                }
            }
            // index % 4 === 3 - Late payers, don't pay (keep as belum_bayar)
        }
    }

    private function createPayment($tagihan, $tanggalBayar)
    {
        Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => $tanggalBayar,
            'jumlah_bayar' => $tagihan->nominal,
            'metode' => rand(0, 1) ? 'tunai' : 'transfer',
            'bukti_transfer' => rand(0, 1) ? 'bukti_' . $tagihan->id . '.jpg' : null,
            'status_konfirmasi' => 'confirmed',
            'confirmed_by' => 1,
            'catatan' => 'Pembayaran ' . $tagihan->jenisPembayaran->nama_pembayaran
        ]);

        // Update tagihan status
        $tagihan->update(['status' => 'sudah_bayar']);
    }
}
