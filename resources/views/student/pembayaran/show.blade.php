@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Detail Pembayaran</h1>
            <div class="flex gap-2">
                @if ($pembayaran->bukti_transfer)
                    <a href="{{ route('student.pembayaran.download', $pembayaran) }}"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Bukti
                    </a>
                @endif
                @if ($pembayaran->status_konfirmasi === 'confirmed')
                    <a href="#" onclick="printReceipt()"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Kwitansi
                    </a>
                @endif
                <a href="{{ route('student.pembayaran') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Status Alert -->
        @if ($pembayaran->status_konfirmasi === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-yellow-900">Pembayaran Sedang Diproses</h4>
                        <p class="text-yellow-800 text-sm">Pembayaran Anda sedang menunggu konfirmasi dari admin. Mohon
                            tunggu 1x24 jam untuk konfirmasi.</p>
                    </div>
                </div>
            </div>
        @elseif($pembayaran->status_konfirmasi === 'confirmed')
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-green-900">Pembayaran Berhasil Dikonfirmasi</h4>
                        <p class="text-green-800 text-sm">Pembayaran Anda telah dikonfirmasi oleh
                            {{ $pembayaran->confirmedBy->name }} pada {{ $pembayaran->updated_at->format('d M Y H:i') }}.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($pembayaran->status_konfirmasi === 'rejected')
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-red-900">Pembayaran Ditolak</h4>
                        <p class="text-red-800 text-sm">Pembayaran Anda ditolak. Silakan lakukan pembayaran ulang atau
                            hubungi admin untuk informasi lebih lanjut.</p>
                        @if ($pembayaran->catatan)
                            <p class="text-red-700 text-sm mt-1"><strong>Alasan:</strong> {{ $pembayaran->catatan }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Info Pembayaran -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Jenis Pembayaran</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $pembayaran->tagihan->jenisPembayaran->nama_pembayaran }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Periode</label>
                    <p class="mt-1 text-lg text-gray-900">
                        @if ($pembayaran->tagihan->bulan)
                            {{ \Carbon\Carbon::create()->month($pembayaran->tagihan->bulan)->format('F') }}
                            {{ $pembayaran->tagihan->tahun }}
                        @else
                            {{ ucfirst($pembayaran->tagihan->semester) }} {{ $pembayaran->tagihan->tahun }}
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tanggal Pembayaran</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $pembayaran->tanggal_bayar->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Jumlah Dibayar</label>
                    <p class="mt-1 text-3xl font-bold text-primary-600">Rp
                        {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Metode Pembayaran</label>
                    <p class="mt-1">
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded capitalize">
                            {{ $pembayaran->metode }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <div class="mt-1">
                        {!! $pembayaran->status_badge !!}
                    </div>
                </div>
            </div>

            @if ($pembayaran->catatan && $pembayaran->status_konfirmasi !== 'rejected')
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500">Catatan</label>
                    <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $pembayaran->catatan }}</p>
                </div>
            @endif
        </div>

        <!-- Bukti Transfer -->
        @if ($pembayaran->bukti_transfer)
            <div class="bg-white rounded-xl shadow-soft p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bukti Transfer</h3>

                <!-- Preview Image -->
                @if (in_array(strtolower(pathinfo($pembayaran->bukti_transfer, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" alt="Bukti Transfer"
                            class="max-w-full h-auto max-h-96 mx-auto rounded-lg shadow-md border">
                    </div>
                @endif

                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">{{ basename($pembayaran->bukti_transfer) }}</p>
                            <p class="text-sm text-gray-500">
                                {{ strtoupper(pathinfo($pembayaran->bukti_transfer, PATHINFO_EXTENSION)) }} File</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" target="_blank"
                            class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat
                        </a>
                        <a href="{{ route('student.pembayaran.download', $pembayaran) }}"
                            class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Info Tagihan -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tagihan Terkait</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nominal Tagihan</label>
                    <p class="mt-1 text-lg text-gray-900">Rp
                        {{ number_format($pembayaran->tagihan->nominal, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Deadline Tagihan</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $pembayaran->tagihan->deadline->format('d M Y') }}</p>
                    @if ($pembayaran->tagihan->is_terlambat)
                        <p class="text-sm text-red-600">Pembayaran terlambat</p>
                    @elseif($pembayaran->tanggal_bayar <= $pembayaran->tagihan->deadline)
                        <p class="text-sm text-green-600">Pembayaran tepat waktu</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status Tagihan</label>
                    <div class="mt-1">
                        {!! $pembayaran->tagihan->status_badge !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if ($pembayaran->status_konfirmasi === 'rejected')
            <div class="bg-white rounded-xl shadow-soft p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pembayaran Ditolak</h3>
                <p class="text-gray-600 mb-6">Silakan lakukan pembayaran ulang untuk tagihan ini</p>
                <a href="{{ route('student.tagihan.pay', $pembayaran->tagihan) }}"
                    class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Bayar Ulang
                </a>
            </div>
        @endif
    </div>

    <script>
        function printReceipt() {
            // Implementasi cetak kwitansi
            window.print();
        }
    </script>
@endsection
