<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPembayaran;
use Illuminate\Http\Request;

class JenisPembayaranController extends Controller
{
    public function index()
    {
        $jenisPembayaran = JenisPembayaran::when(request('search'), function ($query) {
            $query->where('nama_pembayaran', 'like', '%' . request('search') . '%');
        })->paginate(10);

        return view('admin.jenis-pembayaran.index', compact('jenisPembayaran'));
    }

    public function create()
    {
        return view('admin.jenis-pembayaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pembayaran' => 'required|string|max:255|unique:jenis_pembayaran,nama_pembayaran',
            'nominal' => 'required|numeric|min:0',
            'periode' => 'required|in:bulanan,semester',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean'
        ]);

        JenisPembayaran::create($validated);

        return redirect()->route('admin.jenis-pembayaran.index')
            ->with('success', 'Jenis pembayaran berhasil ditambahkan.');
    }

    public function show(JenisPembayaran $jenisPembayaran)
    {
        $jenisPembayaran->load('tagihan.siswa');
        return view('admin.jenis-pembayaran.show', compact('jenisPembayaran'));
    }

    public function edit(JenisPembayaran $jenisPembayaran)
    {
        return view('admin.jenis-pembayaran.edit', compact('jenisPembayaran'));
    }

    public function update(Request $request, JenisPembayaran $jenisPembayaran)
    {
        $validated = $request->validate([
            'nama_pembayaran' => 'required|string|max:255|unique:jenis_pembayaran,nama_pembayaran,' . $jenisPembayaran->id,
            'nominal' => 'required|numeric|min:0',
            'periode' => 'required|in:bulanan,semester',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean'
        ]);

        $jenisPembayaran->update($validated);

        return redirect()->route('admin.jenis-pembayaran.index')
            ->with('success', 'Jenis pembayaran berhasil diperbarui.');
    }

    public function destroy(JenisPembayaran $jenisPembayaran)
    {
        // Check if ada tagihan yang menggunakan jenis pembayaran ini
        if ($jenisPembayaran->tagihan()->count() > 0) {
            return redirect()->route('admin.jenis-pembayaran.index')
                ->with('error', 'Tidak dapat menghapus jenis pembayaran yang sudah digunakan dalam tagihan.');
        }

        $jenisPembayaran->delete();

        return redirect()->route('admin.jenis-pembayaran.index')
            ->with('success', 'Jenis pembayaran berhasil dihapus.');
    }
}
