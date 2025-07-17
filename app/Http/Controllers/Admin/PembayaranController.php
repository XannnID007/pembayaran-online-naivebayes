<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran::with(['tagihan.siswa', 'tagihan.jenisPembayaran', 'confirmedBy'])
            ->when(request('search'), function ($query) {
                $query->whereHas('tagihan.siswa', function ($q) {
                    $q->where('nama', 'like', '%' . request('search') . '%')
                        ->orWhere('nis', 'like', '%' . request('search') . '%');
                });
            })
            ->when(request('status'), function ($query) {
                $query->where('status_konfirmasi', request('status'));
            })
            ->when(request('filter') === 'pending', function ($query) {
                $query->pending();
            })
            ->latest()
            ->paginate(20);

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['tagihan.siswa', 'tagihan.jenisPembayaran', 'confirmedBy']);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function confirm(Request $request, Pembayaran $pembayaran)
    {
        if ($pembayaran->status_konfirmasi !== 'pending') {
            return back()->with('error', 'Pembayaran sudah dikonfirmasi sebelumnya.');
        }

        $pembayaran->update([
            'status_konfirmasi' => 'confirmed',
            'confirmed_by' => auth()->id(),
            'catatan' => $request->catatan
        ]);

        // Update status tagihan
        $pembayaran->tagihan->update(['status' => 'sudah_bayar']);

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function reject(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'catatan' => 'required|string|max:500'
        ]);

        if ($pembayaran->status_konfirmasi !== 'pending') {
            return back()->with('error', 'Pembayaran sudah diproses sebelumnya.');
        }

        $pembayaran->update([
            'status_konfirmasi' => 'rejected',
            'confirmed_by' => auth()->id(),
            'catatan' => $request->catatan
        ]);

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil ditolak.');
    }

    public function create()
    {
        $tagihan = Tagihan::with(['siswa', 'jenisPembayaran'])
            ->belumBayar()
            ->get();

        return view('admin.pembayaran.create', compact('tagihan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode' => 'required|in:tunai,transfer',
            'catatan' => 'nullable|string|max:500'
        ]);

        $tagihan = Tagihan::find($validated['tagihan_id']);

        if ($tagihan->status === 'sudah_bayar') {
            return back()->with('error', 'Tagihan sudah dibayar.');
        }

        $pembayaran = Pembayaran::create([
            'tagihan_id' => $validated['tagihan_id'],
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'metode' => $validated['metode'],
            'status_konfirmasi' => 'confirmed', // Auto confirm untuk input manual admin
            'confirmed_by' => auth()->id(),
            'catatan' => $validated['catatan']
        ]);

        // Update status tagihan
        $tagihan->update(['status' => 'sudah_bayar']);

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function edit(Pembayaran $pembayaran)
    {
        if ($pembayaran->status_konfirmasi === 'confirmed') {
            return back()->with('error', 'Pembayaran yang sudah dikonfirmasi tidak dapat diedit.');
        }

        $tagihan = Tagihan::with(['siswa', 'jenisPembayaran'])
            ->belumBayar()
            ->get();

        return view('admin.pembayaran.edit', compact('pembayaran', 'tagihan'));
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        if ($pembayaran->status_konfirmasi === 'confirmed') {
            return back()->with('error', 'Pembayaran yang sudah dikonfirmasi tidak dapat diedit.');
        }

        $validated = $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode' => 'required|in:tunai,transfer',
            'catatan' => 'nullable|string|max:500'
        ]);

        $pembayaran->update($validated);

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        if ($pembayaran->status_konfirmasi === 'confirmed') {
            return back()->with('error', 'Pembayaran yang sudah dikonfirmasi tidak dapat dihapus.');
        }

        // Delete bukti transfer if exists
        if ($pembayaran->bukti_transfer) {
            Storage::disk('public')->delete($pembayaran->bukti_transfer);
        }

        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }
}
