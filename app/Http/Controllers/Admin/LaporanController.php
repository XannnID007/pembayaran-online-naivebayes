<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filter = [
            'tanggal_mulai' => $request->get('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d')),
            'tanggal_selesai' => $request->get('tanggal_selesai', Carbon::now()->endOfMonth()->format('Y-m-d')),
            'jenis_pembayaran_id' => $request->get('jenis_pembayaran_id'),
            'kelas' => $request->get('kelas'),
        ];

        // Summary Data
        $summary = [
            'total_pembayaran' => $this->getTotalPembayaran($filter),
            'total_tagihan' => $this->getTotalTagihan($filter),
            'pembayaran_pending' => $this->getPembayaranPending(),
            'siswa_belum_bayar' => $this->getSiswaBelumBayar($filter),
        ];

        // Pembayaran per Jenis
        $pembayaranPerJenis = $this->getPembayaranPerJenis($filter);

        // Pembayaran per Bulan (chart data)
        $pembayaranPerBulan = $this->getPembayaranPerBulan();

        // Top 10 Pembayar
        $topPembayar = $this->getTopPembayar($filter);

        // Tunggakan Terbesar
        $tunggakanTerbesar = $this->getTunggakanTerbesar();

        $jenisPembayaran = JenisPembayaran::aktif()->get();
        $kelasList = Siswa::select('kelas')->distinct()->pluck('kelas');

        return view('admin.laporan.index', compact(
            'filter',
            'summary',
            'pembayaranPerJenis',
            'pembayaranPerBulan',
            'topPembayar',
            'tunggakanTerbesar',
            'jenisPembayaran',
            'kelasList'
        ));
    }

    private function getTotalPembayaran($filter)
    {
        return Pembayaran::confirmed()
            ->whereBetween('tanggal_bayar', [$filter['tanggal_mulai'], $filter['tanggal_selesai']])
            ->when($filter['jenis_pembayaran_id'], function ($query) use ($filter) {
                $query->whereHas('tagihan', function ($q) use ($filter) {
                    $q->where('jenis_pembayaran_id', $filter['jenis_pembayaran_id']);
                });
            })
            ->when($filter['kelas'], function ($query) use ($filter) {
                $query->whereHas('tagihan.siswa', function ($q) use ($filter) {
                    $q->where('kelas', $filter['kelas']);
                });
            })
            ->sum('jumlah_bayar');
    }

    private function getTotalTagihan($filter)
    {
        return Tagihan::whereBetween('deadline', [$filter['tanggal_mulai'], $filter['tanggal_selesai']])
            ->when($filter['jenis_pembayaran_id'], function ($query) use ($filter) {
                $query->where('jenis_pembayaran_id', $filter['jenis_pembayaran_id']);
            })
            ->when($filter['kelas'], function ($query) use ($filter) {
                $query->whereHas('siswa', function ($q) use ($filter) {
                    $q->where('kelas', $filter['kelas']);
                });
            })
            ->sum('nominal');
    }

    private function getPembayaranPending()
    {
        return Pembayaran::pending()->count();
    }

    private function getSiswaBelumBayar($filter)
    {
        return Tagihan::belumBayar()
            ->whereBetween('deadline', [$filter['tanggal_mulai'], $filter['tanggal_selesai']])
            ->when($filter['jenis_pembayaran_id'], function ($query) use ($filter) {
                $query->where('jenis_pembayaran_id', $filter['jenis_pembayaran_id']);
            })
            ->when($filter['kelas'], function ($query) use ($filter) {
                $query->whereHas('siswa', function ($q) use ($filter) {
                    $q->where('kelas', $filter['kelas']);
                });
            })
            ->distinct('siswa_id')
            ->count('siswa_id');
    }

    private function getPembayaranPerJenis($filter)
    {
        return DB::table('pembayaran')
            ->join('tagihan', 'pembayaran.tagihan_id', '=', 'tagihan.id')
            ->join('jenis_pembayaran', 'tagihan.jenis_pembayaran_id', '=', 'jenis_pembayaran.id')
            ->where('pembayaran.status_konfirmasi', 'confirmed')
            ->whereBetween('pembayaran.tanggal_bayar', [$filter['tanggal_mulai'], $filter['tanggal_selesai']])
            ->when($filter['jenis_pembayaran_id'], function ($query) use ($filter) {
                $query->where('jenis_pembayaran.id', $filter['jenis_pembayaran_id']);
            })
            ->select('jenis_pembayaran.nama_pembayaran', DB::raw('SUM(pembayaran.jumlah_bayar) as total'))
            ->groupBy('jenis_pembayaran.id', 'jenis_pembayaran.nama_pembayaran')
            ->get();
    }

    private function getPembayaranPerBulan()
    {
        return DB::table('pembayaran')
            ->where('status_konfirmasi', 'confirmed')
            ->where('tanggal_bayar', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('YEAR(tanggal_bayar) as year'),
                DB::raw('MONTH(tanggal_bayar) as month'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    private function getTopPembayar($filter)
    {
        return DB::table('pembayaran')
            ->join('tagihan', 'pembayaran.tagihan_id', '=', 'tagihan.id')
            ->join('siswa', 'tagihan.siswa_id', '=', 'siswa.id')
            ->where('pembayaran.status_konfirmasi', 'confirmed')
            ->whereBetween('pembayaran.tanggal_bayar', [$filter['tanggal_mulai'], $filter['tanggal_selesai']])
            ->select('siswa.nama', 'siswa.nis', 'siswa.kelas', DB::raw('SUM(pembayaran.jumlah_bayar) as total'))
            ->groupBy('siswa.id', 'siswa.nama', 'siswa.nis', 'siswa.kelas')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
    }

    private function getTunggakanTerbesar()
    {
        return DB::table('tagihan')
            ->join('siswa', 'tagihan.siswa_id', '=', 'siswa.id')
            ->join('jenis_pembayaran', 'tagihan.jenis_pembayaran_id', '=', 'jenis_pembayaran.id')
            ->where('tagihan.status', 'belum_bayar')
            ->where('tagihan.deadline', '<', Carbon::now())
            ->select(
                'siswa.nama',
                'siswa.nis',
                'siswa.kelas',
                'jenis_pembayaran.nama_pembayaran',
                'tagihan.nominal',
                'tagihan.deadline'
            )
            ->orderBy('tagihan.nominal', 'desc')
            ->limit(10)
            ->get();
    }

    public function export(Request $request, $type)
    {
        // Implementation for export (Excel/PDF)
        // This would require additional packages like maatwebsite/excel or dompdf

        return response()->json(['message' => 'Export feature will be implemented']);
    }
}
