<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NaiveBayesService;
use App\Models\KlasifikasiSiswa;
use Illuminate\Http\Request;

class KlasifikasiController extends Controller
{
    protected $naiveBayesService;

    public function __construct(NaiveBayesService $naiveBayesService)
    {
        $this->naiveBayesService = $naiveBayesService;
    }

    public function index()
    {
        try {
            // Get klasifikasi data with pagination
            $klasifikasi = KlasifikasiSiswa::with('siswa')
                ->latest('tanggal_prediksi')
                ->paginate(20);

            // Get classification report
            $report = $this->naiveBayesService->getClassificationReport();

            return view('admin.klasifikasi.index', compact('klasifikasi', 'report'));
        } catch (\Exception $e) {
            // Jika ada error, tampilkan halaman dengan data kosong
            $klasifikasi = collect([]); // Empty collection
            $report = [
                'total_classified' => 0,
                'distribution' => [
                    'pembayar_disiplin' => ['count' => 0, 'percentage' => 0],
                    'pembayar_terlambat' => ['count' => 0, 'percentage' => 0],
                    'pembayar_selektif' => ['count' => 0, 'percentage' => 0],
                ],
                'average_confidence' => 0,
                'recent_classifications' => collect([])
            ];

            return view('admin.klasifikasi.index', compact('klasifikasi', 'report'))
                ->with('warning', 'Belum ada data klasifikasi. Silakan jalankan klasifikasi terlebih dahulu.');
        }
    }

    public function show($id)
    {
        $klasifikasi = KlasifikasiSiswa::with('siswa')->findOrFail($id);

        return response()->json($klasifikasi);
    }

    public function runClassification(Request $request)
    {
        try {
            $results = $this->naiveBayesService->classifyAllStudents();

            return redirect()->route('admin.klasifikasi')
                ->with('success', 'Klasifikasi berhasil dijalankan. ' . count($results['classifications']) . ' siswa telah diklasifikasi.');
        } catch (\Exception $e) {
            return redirect()->route('admin.klasifikasi')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
