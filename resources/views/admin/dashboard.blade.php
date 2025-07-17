@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="space-y-6">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Siswa -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Siswa</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalSiswa ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Tagihan Belum Bayar -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tagihan Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $tagihanPending ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp
                            {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Pembayaran Pending -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.963-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Konfirmasi Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pembayaranPending ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pembayaran Per Bulan -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Pembayaran Per Bulan</h3>
                    <select
                        class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option>2025</option>
                        <option>2024</option>
                    </select>
                </div>
                <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500">Chart akan ditampilkan di sini</p>
                </div>
            </div>

            <!-- Klasifikasi Siswa -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Klasifikasi Pola Pembayaran</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">Pembayar Disiplin</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $klasifikasiDisiplin ?? 0 }} siswa</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">Pembayar Terlambat</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $klasifikasiTerlambat ?? 0 }} siswa</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">Pembayar Selektif</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $klasifikasiSelektif ?? 0 }} siswa</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pembayaran Terbaru -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Pembayaran Terbaru</h3>
                    <a href="{{ route('admin.pembayaran.index') }}"
                        class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                        Lihat Semua
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($pembayaranTerbaru ?? [] as $pembayaran)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                    <span
                                        class="text-sm font-medium text-primary-600">{{ substr($pembayaran->tagihan->siswa->nama, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $pembayaran->tagihan->siswa->nama }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $pembayaran->tagihan->jenisPembayaran->nama_pembayaran }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">Rp
                                    {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                                <div class="text-xs">{!! $pembayaran->status_badge !!}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-500 text-sm">Belum ada pembayaran terbaru</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tagihan Terlambat -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Tagihan Terlambat</h3>
                    <a href="{{ route('admin.tagihan.index') }}?filter=terlambat"
                        class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                        Lihat Semua
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($tagihanTerlambat ?? [] as $tagihan)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                    <span
                                        class="text-sm font-medium text-red-600">{{ substr($tagihan->siswa->nama, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $tagihan->siswa->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $tagihan->jenisPembayaran->nama_pembayaran }} -
                                        {{ $tagihan->deadline->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">Rp
                                    {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                                <span
                                    class="text-xs text-red-600 font-medium">{{ $tagihan->deadline->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500 text-sm">Tidak ada tagihan terlambat</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto refresh data setiap 30 detik
        setInterval(function() {
            // Bisa ditambahkan AJAX untuk update data real-time
        }, 30000);
    </script>
@endsection
