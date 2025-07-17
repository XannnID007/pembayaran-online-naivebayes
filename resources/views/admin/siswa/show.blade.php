@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Detail Siswa: {{ $siswa->nama }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.siswa.edit', $siswa) }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.siswa.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Info Siswa -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Siswa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nama Lengkap</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $siswa->nama }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">NIS</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $siswa->nis }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Kelas</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $siswa->kelas }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $siswa->user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">No. HP</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $siswa->no_hp ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <div class="mt-1">
                        @if ($siswa->status === 'aktif')
                            <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded">Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded">Non-Aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            @if ($siswa->alamat)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500">Alamat</label>
                    <p class="mt-1 text-gray-900">{{ $siswa->alamat }}</p>
                </div>
            @endif
        </div>

        <!-- Statistik Pembayaran -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-soft p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Tagihan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $siswa->tagihan->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-soft p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Sudah Bayar</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $siswa->tagihan->where('status', 'sudah_bayar')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-soft p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Belum Bayar</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $siswa->tagihan->where('status', 'belum_bayar')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-soft p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Tunggakan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp
                            {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Tagihan -->
        <div class="bg-white rounded-xl shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Tagihan</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis
                                Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nominal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deadline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($siswa->tagihan as $tagihan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tagihan->jenisPembayaran->nama_pembayaran }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($tagihan->bulan)
                                        {{ \Carbon\Carbon::create()->month($tagihan->bulan)->format('F') }}
                                        {{ $tagihan->tahun }}
                                    @else
                                        {{ ucfirst($tagihan->semester) }} {{ $tagihan->tahun }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tagihan->deadline->format('d M Y') }}
                                    @if ($tagihan->is_terlambat)
                                        <div class="text-xs text-red-600">{{ $tagihan->deadline->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $tagihan->status_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.tagihan.show', $tagihan) }}"
                                        class="text-primary-600 hover:text-primary-900">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada tagihan untuk siswa ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Klasifikasi Siswa -->
        @if ($siswa->klasifikasi->count() > 0)
            <div class="bg-white rounded-xl shadow-soft p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Klasifikasi Pola Pembayaran</h3>
                @php $klasifikasi = $siswa->klasifikasi->first() @endphp
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center mb-2">
                            <span class="text-gray-600 mr-3">Kategori:</span>
                            {!! $klasifikasi->kategori_badge !!}
                        </div>
                        <div class="flex items-center">
                            <span class="text-gray-600 mr-3">Confidence Score:</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-primary-500 h-2 rounded-full"
                                        style="width: {{ $klasifikasi->confidence_score * 100 }}%"></div>
                                </div>
                                <span
                                    class="text-sm text-gray-900">{{ number_format($klasifikasi->confidence_score * 100, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Tanggal Prediksi</p>
                        <p class="text-sm text-gray-900">{{ $klasifikasi->tanggal_prediksi->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
