<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\KlasifikasiSiswa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalSiswa' => Siswa::aktif()->count(),
            'tagihanPending' => Tagihan::belumBayar()->count(),
            'totalPendapatan' => Pembayaran::confirmed()->sum('jumlah_bayar'),
            'pembayaranPending' => Pembayaran::pending()->count(),

            // Klasifikasi data
            'klasifikasiDisiplin' => KlasifikasiSiswa::where('kategori_prediksi', 'pembayar_disiplin')->count(),
            'klasifikasiTerlambat' => KlasifikasiSiswa::where('kategori_prediksi', 'pembayar_terlambat')->count(),
            'klasifikasiSelektif' => KlasifikasiSiswa::where('kategori_prediksi', 'pembayar_selektif')->count(),

            // Recent data
            'pembayaranTerbaru' => Pembayaran::with(['tagihan.siswa', 'tagihan.jenisPembayaran'])
                ->latest()
                ->take(5)
                ->get(),
            'tagihanTerlambat' => Tagihan::with(['siswa', 'jenisPembayaran'])
                ->terlambat()
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', $data);
    }
}
