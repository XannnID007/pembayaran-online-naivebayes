<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with(['user', 'tagihan'])
            ->when(request('search'), function ($query) {
                $query->where('nama', 'like', '%' . request('search') . '%')
                    ->orWhere('nis', 'like', '%' . request('search') . '%');
            })
            ->paginate(10);

        return view('admin.siswa.index', compact('siswa'));
    }

    public function create()
    {
        return view('admin.siswa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|unique:siswa,nis',
            'kelas' => 'required|string|max:10',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Create user account
        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
        ]);

        // Create siswa record
        Siswa::create([
            'user_id' => $user->id,
            'nama' => $validated['nama'],
            'nis' => $validated['nis'],
            'kelas' => $validated['kelas'],
            'alamat' => $validated['alamat'],
            'no_hp' => $validated['no_hp'],
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        $siswa->load(['user', 'tagihan.jenisPembayaran', 'klasifikasi']);
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        return view('admin.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|unique:siswa,nis,' . $siswa->id,
            'kelas' => 'required|string|max:10',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'status' => 'required|in:aktif,non_aktif',
        ]);

        $siswa->update($validated);
        $siswa->user->update(['name' => $validated['nama']]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->user->delete(); // akan cascade delete siswa juga
        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
