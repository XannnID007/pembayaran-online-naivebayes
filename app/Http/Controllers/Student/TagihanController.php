<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TagihanController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()->siswa;

        $tagihan = $siswa->tagihan()
            ->with(['jenisPembayaran', 'pembayaran'])
            ->when(request('jenis'), function ($query) {
                $query->whereHas('jenisPembayaran', function ($q) {
                    $q->where('nama_pembayaran', request('jenis'));
                });
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->orderBy('deadline')
            ->paginate(15);

        // Summary data
        $summary = [
            'total_tagihan' => $siswa->tagihan()->count(),
            'belum_bayar' => $siswa->tagihan()->belumBayar()->count(),
            'sudah_bayar' => $siswa->tagihan()->sudahBayar()->count(),
            'terlambat' => $siswa->tagihan()->terlambat()->count(),
            'total_nominal_belum_bayar' => $siswa->tagihan()->belumBayar()->sum('nominal'),
        ];

        return view('student.tagihan.index', compact('tagihan', 'summary'));
    }

    public function show(Tagihan $tagihan)
    {
        // Pastikan tagihan milik siswa yang login
        if ($tagihan->siswa_id !== auth()->user()->siswa->id) {
            abort(403, 'Unauthorized');
        }

        $tagihan->load(['jenisPembayaran', 'pembayaran.confirmedBy', 'siswa']);

        return view('student.tagihan.show', compact('tagihan'));
    }

    public function pay(Tagihan $tagihan)
    {
        // Pastikan tagihan milik siswa yang login
        if ($tagihan->siswa_id !== auth()->user()->siswa->id) {
            abort(403, 'Unauthorized');
        }

        if ($tagihan->status === 'sudah_bayar') {
            return redirect()->route('student.tagihan')
                ->with('error', 'Tagihan sudah dibayar.');
        }

        $tagihan->load(['jenisPembayaran', 'siswa']);

        return view('student.tagihan.pay', compact('tagihan'));
    }

    public function storePay(Request $request, Tagihan $tagihan)
    {
        // Pastikan tagihan milik siswa yang login
        if ($tagihan->siswa_id !== auth()->user()->siswa->id) {
            abort(403, 'Unauthorized');
        }

        if ($tagihan->status === 'sudah_bayar') {
            return redirect()->route('student.tagihan')
                ->with('error', 'Tagihan sudah dibayar.');
        }

        $validated = $request->validate([
            'tanggal_bayar' => 'required|date|before_or_equal:today',
            'jumlah_bayar' => 'required|numeric|min:0|max:' . $tagihan->nominal,
            'metode' => 'required|in:tunai,transfer',
            'bukti_transfer' => 'required_if:metode,transfer|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan' => 'nullable|string|max:500'
        ]);

        // Handle file upload
        $buktiTransferPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiTransferPath = $request->file('bukti_transfer')->store('bukti_pembayaran', 'public');
        }

        Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'metode' => $validated['metode'],
            'bukti_transfer' => $buktiTransferPath,
            'status_konfirmasi' => 'pending',
            'catatan' => $validated['catatan']
        ]);

        return redirect()->route('student.tagihan')
            ->with('success', 'Pembayaran berhasil disubmit. Menunggu konfirmasi admin.');
    }
}
