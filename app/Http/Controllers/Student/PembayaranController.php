<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()->siswa;

        $pembayaran = Pembayaran::whereHas('tagihan', function ($query) use ($siswa) {
            $query->where('siswa_id', $siswa->id);
        })
            ->with(['tagihan.jenisPembayaran', 'confirmedBy'])
            ->when(request('status'), function ($query) {
                $query->where('status_konfirmasi', request('status'));
            })
            ->when(request('jenis'), function ($query) {
                $query->whereHas('tagihan.jenisPembayaran', function ($q) {
                    $q->where('nama_pembayaran', request('jenis'));
                });
            })
            ->latest()
            ->paginate(15);

        // Summary data
        $summary = [
            'total_pembayaran' => Pembayaran::whereHas('tagihan', function ($query) use ($siswa) {
                $query->where('siswa_id', $siswa->id);
            })->count(),
            'confirmed' => Pembayaran::whereHas('tagihan', function ($query) use ($siswa) {
                $query->where('siswa_id', $siswa->id);
            })->confirmed()->count(),
            'pending' => Pembayaran::whereHas('tagihan', function ($query) use ($siswa) {
                $query->where('siswa_id', $siswa->id);
            })->pending()->count(),
            'rejected' => Pembayaran::whereHas('tagihan', function ($query) use ($siswa) {
                $query->where('siswa_id', $siswa->id);
            })->where('status_konfirmasi', 'rejected')->count(),
            'total_dibayar' => Pembayaran::whereHas('tagihan', function ($query) use ($siswa) {
                $query->where('siswa_id', $siswa->id);
            })->confirmed()->sum('jumlah_bayar'),
        ];

        return view('student.pembayaran.index', compact('pembayaran', 'summary'));
    }

    public function show(Pembayaran $pembayaran)
    {
        // Pastikan pembayaran milik siswa yang login
        if ($pembayaran->tagihan->siswa_id !== auth()->user()->siswa->id) {
            abort(403, 'Unauthorized');
        }

        $pembayaran->load(['tagihan.jenisPembayaran', 'tagihan.siswa', 'confirmedBy']);

        return view('student.pembayaran.show', compact('pembayaran'));
    }

    public function download(Pembayaran $pembayaran)
    {
        // Pastikan pembayaran milik siswa yang login
        if ($pembayaran->tagihan->siswa_id !== auth()->user()->siswa->id) {
            abort(403, 'Unauthorized');
        }

        if (!$pembayaran->bukti_transfer) {
            return back()->with('error', 'Bukti transfer tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($pembayaran->bukti_transfer)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        $filePath = Storage::disk('public')->path($pembayaran->bukti_transfer);
        $fileName = 'Bukti_Pembayaran_' . $pembayaran->id . '_' . $pembayaran->tagihan->siswa->nama . '.' . pathinfo($pembayaran->bukti_transfer, PATHINFO_EXTENSION);

        return Response::download($filePath, $fileName);
    }

    public function receipt(Pembayaran $pembayaran)
    {
        // Pastikan pembayaran milik siswa yang login
        if ($pembayaran->tagihan->siswa_id !== auth()->user()->siswa->id) {
            abort(403, 'Unauthorized');
        }

        if ($pembayaran->status_konfirmasi !== 'confirmed') {
            return back()->with('error', 'Kwitansi hanya tersedia untuk pembayaran yang sudah dikonfirmasi.');
        }

        $pembayaran->load(['tagihan.jenisPembayaran', 'tagihan.siswa', 'confirmedBy']);

        return view('student.pembayaran.receipt', compact('pembayaran'));
    }
}
