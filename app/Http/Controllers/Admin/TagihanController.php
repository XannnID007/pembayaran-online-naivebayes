<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TagihanController extends Controller
{
    public function index()
    {
        $tagihan = Tagihan::with(['siswa', 'jenisPembayaran'])
            ->when(request('search'), function ($query) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('nama', 'like', '%' . request('search') . '%')
                        ->orWhere('nis', 'like', '%' . request('search') . '%');
                });
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('filter') === 'terlambat', function ($query) {
                $query->terlambat();
            })
            ->latest()
            ->paginate(20);

        return view('admin.tagihan.index', compact('tagihan'));
    }

    public function create()
    {
        $siswa = Siswa::aktif()->get();
        $jenisPembayaran = JenisPembayaran::aktif()->get();

        return view('admin.tagihan.create', compact('siswa', 'jenisPembayaran'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'bulan' => 'nullable|string|max:2',
            'semester' => 'nullable|string|in:ganjil,genap',
            'tahun' => 'required|integer|min:2020|max:2030',
            'nominal' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
        ]);

        // Validasi tambahan berdasarkan periode
        $jenisPembayaran = JenisPembayaran::find($validated['jenis_pembayaran_id']);

        if ($jenisPembayaran->periode === 'bulanan' && empty($validated['bulan'])) {
            return back()->withErrors(['bulan' => 'Bulan harus diisi untuk pembayaran bulanan.']);
        }

        if ($jenisPembayaran->periode === 'semester' && empty($validated['semester'])) {
            return back()->withErrors(['semester' => 'Semester harus diisi untuk pembayaran semester.']);
        }

        // Check duplikasi
        $exists = Tagihan::where('siswa_id', $validated['siswa_id'])
            ->where('jenis_pembayaran_id', $validated['jenis_pembayaran_id'])
            ->where('tahun', $validated['tahun'])
            ->when($validated['bulan'], function ($query) use ($validated) {
                $query->where('bulan', $validated['bulan']);
            })
            ->when($validated['semester'], function ($query) use ($validated) {
                $query->where('semester', $validated['semester']);
            })
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Tagihan untuk periode ini sudah ada.']);
        }

        Tagihan::create($validated);

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil dibuat.');
    }

    public function show(Tagihan $tagihan)
    {
        $tagihan->load(['siswa', 'jenisPembayaran', 'pembayaran']);
        return view('admin.tagihan.show', compact('tagihan'));
    }

    public function edit(Tagihan $tagihan)
    {
        $siswa = Siswa::aktif()->get();
        $jenisPembayaran = JenisPembayaran::aktif()->get();

        return view('admin.tagihan.edit', compact('tagihan', 'siswa', 'jenisPembayaran'));
    }

    public function update(Request $request, Tagihan $tagihan)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'bulan' => 'nullable|string|max:2',
            'semester' => 'nullable|string|in:ganjil,genap',
            'tahun' => 'required|integer|min:2020|max:2030',
            'nominal' => 'required|numeric|min:0',
            'deadline' => 'required|date',
            'status' => 'required|in:belum_bayar,sudah_bayar',
        ]);

        $tagihan->update($validated);

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Tagihan $tagihan)
    {
        // Check if ada pembayaran
        if ($tagihan->pembayaran()->count() > 0) {
            return redirect()->route('admin.tagihan.index')
                ->with('error', 'Tidak dapat menghapus tagihan yang sudah memiliki pembayaran.');
        }

        $tagihan->delete();

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    public function generateBulk(Request $request)
    {
        $validated = $request->validate([
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'kelas' => 'nullable|string',
            'bulan' => 'nullable|string|max:2',
            'semester' => 'nullable|string|in:ganjil,genap',
            'tahun' => 'required|integer|min:2020|max:2030',
            'deadline' => 'required|date|after:today',
        ]);

        $jenisPembayaran = JenisPembayaran::find($validated['jenis_pembayaran_id']);

        // Get siswa berdasarkan kelas (optional)
        $siswaQuery = Siswa::aktif();
        if ($validated['kelas']) {
            $siswaQuery->where('kelas', $validated['kelas']);
        }
        $siswaList = $siswaQuery->get();

        $created = 0;
        $skipped = 0;

        foreach ($siswaList as $siswa) {
            // Check duplikasi
            $exists = Tagihan::where('siswa_id', $siswa->id)
                ->where('jenis_pembayaran_id', $validated['jenis_pembayaran_id'])
                ->where('tahun', $validated['tahun'])
                ->when($validated['bulan'], function ($query) use ($validated) {
                    $query->where('bulan', $validated['bulan']);
                })
                ->when($validated['semester'], function ($query) use ($validated) {
                    $query->where('semester', $validated['semester']);
                })
                ->exists();

            if (!$exists) {
                Tagihan::create([
                    'siswa_id' => $siswa->id,
                    'jenis_pembayaran_id' => $validated['jenis_pembayaran_id'],
                    'bulan' => $validated['bulan'],
                    'semester' => $validated['semester'],
                    'tahun' => $validated['tahun'],
                    'nominal' => $jenisPembayaran->nominal,
                    'deadline' => $validated['deadline'],
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        return redirect()->route('admin.tagihan.index')
            ->with('success', "Generate tagihan selesai. $created tagihan dibuat, $skipped tagihan dilewati (sudah ada).");
    }
}
