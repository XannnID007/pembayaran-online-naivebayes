@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Pembayaran</h1>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-soft p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Pembayaran</p>
                        <p class="text-xl font-bold text-gray-900">{{ $summary['total_pembayaran'] }}</p>
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
                        <p class="text-sm text-gray-600">Dikonfirmasi</p>
                        <p class="text-xl font-bold text-gray-900">{{ $summary['confirmed'] }}</p>
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
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-xl font-bold text-gray-900">{{ $summary['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-soft p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Dibayar</p>
                        <p class="text-xl font-bold text-gray-900">Rp
                            {{ number_format($summary['total_dibayar'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <select name="status"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi
                    </option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <select name="jenis"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Jenis</option>
                    <option value="SPP" {{ request('jenis') === 'SPP' ? 'selected' : '' }}>SPP</option>
                    <option value="UTS" {{ request('jenis') === 'UTS' ? 'selected' : '' }}>UTS</option>
                    <option value="UAS" {{ request('jenis') === 'UAS' ? 'selected' : '' }}>UAS</option>
                </select>

                <button type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Filter
                </button>

                @if (request()->hasAny(['status', 'jenis']))
                    <a href="{{ route('student.pembayaran') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Daftar Pembayaran -->
        <div class="space-y-4">
            @forelse($pembayaran as $p)
                <div class="bg-white rounded-xl shadow-soft p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $p->tagihan->jenisPembayaran->nama_pembayaran }}</h3>
                                <div>{!! $p->status_badge !!}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">Tanggal Bayar:</span>
                                    {{ $p->tanggal_bayar->format('d M Y') }}
                                </div>
                                <div>
                                    <span class="font-medium">Metode:</span>
                                    {{ ucfirst($p->metode) }}
                                </div>
                                <div>
                                    <span class="font-medium">Jumlah:</span>
                                    <span class="text-lg font-bold text-gray-900">Rp
                                        {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            @if ($p->catatan)
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-gray-700">Catatan:</span>
                                    <p class="text-sm text-gray-600">{{ $p->catatan }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('student.pembayaran.show', $p) }}"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </a>

                            @if ($p->bukti_transfer)
                                <a href="{{ route('student.pembayaran.download', $p) }}"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-soft p-8 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500">Belum ada riwayat pembayaran</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($pembayaran->hasPages())
            <div class="bg-white rounded-xl shadow-soft p-6">
                {{ $pembayaran->links() }}
            </div>
        @endif
    </div>
@endsection
