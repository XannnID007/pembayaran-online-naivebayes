{{-- resources/views/student/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl shadow-soft p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Selamat Datang,
                        {{ Auth::user()->siswa->nama ?? Auth::user()->name }}!</h1>
                    <p class="text-primary-100">NIS: {{ Auth::user()->siswa->nis ?? '-' }} | Kelas:
                        {{ Auth::user()->siswa->kelas ?? '-' }}</p>
                </div>
                <div class="hidden md:block">
                    <svg class="w-20 h-20 text-primary-200" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Status Ringkasan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Tagihan -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Tagihan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sudah Dibayar -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Sudah Dibayar</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($sudahDibayar ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Belum Dibayar -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Belum Dibayar</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($belumDibayar ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tagihan Mendesak & Riwayat -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tagihan Mendesak -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Tagihan Mendesak</h3>
                    <a href="{{ route('student.tagihan') }}"
                        class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                        Lihat Semua
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($tagihanMendesak ?? [] as $tagihan)
                        <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-red-900">{{ $tagihan->jenisPembayaran->nama_pembayaran }}
                                    </h4>
                                    <p class="text-sm text-red-700">
                                        @if ($tagihan->bulan)
                                            {{ \Carbon\Carbon::create()->month($tagihan->bulan)->format('F') }}
                                            {{ $tagihan->tahun }}
                                        @else
                                            {{ ucfirst($tagihan->semester) }} {{ $tagihan->tahun }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-red-600 mt-1">
                                        Deadline: {{ $tagihan->deadline->format('d M Y') }}
                                        <span class="font-medium">({{ $tagihan->deadline->diffForHumans() }})</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-red-900">Rp
                                        {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                                    <a href="{{ route('student.tagihan') }}?pay={{ $tagihan->id }}"
                                        class="inline-flex items-center mt-2 px-3 py-1 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">
                                        Bayar Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500 text-sm">Tidak ada tagihan mendesak</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pembayaran Terbaru -->
            <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Pembayaran Terbaru</h3>
                    <a href="{{ route('student.pembayaran') }}"
                        class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                        Lihat Semua
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($pembayaranTerbaru ?? [] as $pembayaran)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $pembayaran->tagihan->jenisPembayaran->nama_pembayaran }}</p>
                                    <p class="text-xs text-gray-500">{{ $pembayaran->tanggal_bayar->format('d M Y') }}</p>
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
                            <p class="text-gray-500 text-sm">Belum ada riwayat pembayaran</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Aksi Cepat</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('student.tagihan') }}"
                    class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors group">
                    <svg class="w-8 h-8 text-gray-400 group-hover:text-primary-600 mb-2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-primary-700">Lihat Tagihan</span>
                </a>

                <a href="{{ route('student.tagihan') }}?action=pay"
                    class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors group">
                    <svg class="w-8 h-8 text-gray-400 group-hover:text-primary-600 mb-2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-primary-700">Bayar Online</span>
                </a>

                <a href="{{ route('student.pembayaran') }}"
                    class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors group">
                    <svg class="w-8 h-8 text-gray-400 group-hover:text-primary-600 mb-2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-primary-700">Riwayat Bayar</span>
                </a>

                <a href="#" onclick="downloadReceipt()"
                    class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors group">
                    <svg class="w-8 h-8 text-gray-400 group-hover:text-primary-600 mb-2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-primary-700">Download Bukti</span>
                </a>
            </div>
        </div>

        <!-- Pengumuman -->
        @if (isset($pengumuman) && $pengumuman)
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <div>
                        <h4 class="text-lg font-medium text-blue-900 mb-2">Pengumuman</h4>
                        <p class="text-blue-800">{{ $pengumuman }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function downloadReceipt() {
            // Implementasi download bukti pembayaran
            alert('Fitur download akan segera tersedia');
        }

        // Auto refresh untuk notifikasi baru
        setInterval(function() {
            // Bisa ditambahkan AJAX untuk cek notifikasi baru
        }, 60000);
    </script>
@endsection
