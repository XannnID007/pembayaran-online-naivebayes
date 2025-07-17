<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan.');
        }

        $totalTagihan = $siswa->tagihan()->sum('nominal');
        $sudahDibayar = $siswa->tagihan()->sudahBayar()->sum('nominal');
        $belumDibayar = $totalTagihan - $sudahDibayar;

        $data = [
            'totalTagihan' => $totalTagihan,
            'sudahDibayar' => $sudahDibayar,
            'belumDibayar' => $belumDibayar,

            // Tagihan mendesak (deadline dalam 7 hari)
            'tagihanMendesak' => $siswa->tagihan()
                ->with('jenisPembayaran')
                ->belumBayar()
                ->where('deadline', '<=', Carbon::now()->addDays(7))
                ->orderBy('deadline')
                ->take(3)
                ->get(),

            // Pembayaran terbaru
            'pembayaranTerbaru' => Pembayaran::whereHas('tagihan', function ($query) use ($siswa) {
                $query->where('siswa_id', $siswa->id);
            })
                ->with(['tagihan.jenisPembayaran'])
                ->latest()
                ->take(5)
                ->get(),

            'pengumuman' => 'Pembayaran SPP bulan ini berakhir tanggal 15. Jangan lupa untuk membayar tepat waktu!'
        ];

        return view('student.dashboard', $data);
    }
}
