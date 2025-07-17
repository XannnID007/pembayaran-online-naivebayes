@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Detail Tagihan</h1>
            <div class="flex gap-2">
                @if ($tagihan->status === 'belum_bayar')
                    <a href="{{ route('student.tagihan.pay', $tagihan) }}"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Bayar Sekarang
                    </a>
                @endif
                <a href="{{ route('student.tagihan') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Info Tagihan -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Tagihan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Siswa</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $tagihan->siswa->nama }}</p>
                    <p class="text-sm text-gray-500">{{ $tagihan->siswa->nis }} - {{ $tagihan->siswa->kelas }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Jenis Pembayaran</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $tagihan->jenisPembayaran->nama_pembayaran }}</p>
                    @if ($tagihan->jenisPembayaran->deskripsi)
                        <p class="text-sm text-gray-500">{{ $tagihan->jenisPembayaran->deskripsi }}</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Periode</label>
                    <p class="mt-1 text-lg text-gray-900">
                        @if ($tagihan->bulan)
                            {{ \Carbon\Carbon::create()->month($tagihan->bulan)->format('F') }} {{ $tagihan->tahun }}
                        @else
                            {{ ucfirst($tagihan->semester) }} {{ $tagihan->tahun }}
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nominal</label>
                    <p class="mt-1 text-3xl font-bold text-primary-600">Rp
                        {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Deadline</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $tagihan->deadline->format('d M Y') }}</p>
                    @if ($tagihan->is_terlambat)
                        <p class="text-sm text-red-600 font-medium">Terlambat {{ $tagihan->deadline->diffForHumans() }}</p>
                    @elseif($tagihan->deadline->diffInDays() <= 7)
                        <p class="text-sm text-yellow-600 font-medium">{{ $tagihan->deadline->diffForHumans() }}</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <div class="mt-1">
                        {!! $tagihan->status_badge !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Pembayaran -->
        @if ($tagihan->pembayaran->count() > 0)
            <div class="bg-white rounded-xl shadow-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Pembayaran</h3>
                </div>

                <div class="p-6 space-y-4">
                    @foreach ($tagihan->pembayaran as $pembayaran)
                        <div
                            class="border border-gray-200 rounded-lg p-4 {{ $pembayaran->status_konfirmasi === 'confirmed' ? 'bg-green-50 border-green-200' : ($pembayaran->status_konfirmasi === 'rejected' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200') }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="font-medium text-gray-900">Pembayaran
                                            {{ $pembayaran->tanggal_bayar->format('d M Y') }}</span>
                                        {!! $pembayaran->status_badge !!}
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-600">Jumlah:</span>
                                            <span class="font-medium text-gray-900">Rp
                                                {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Metode:</span>
                                            <span
                                                class="font-medium text-gray-900 capitalize">{{ $pembayaran->metode }}</span>
                                        </div>
                                        @if ($pembayaran->confirmedBy)
                                            <div>
                                                <span class="text-gray-600">Dikonfirmasi:</span>
                                                <span
                                                    class="font-medium text-gray-900">{{ $pembayaran->confirmedBy->name }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($pembayaran->catatan)
                                        <div class="mt-3">
                                            <span class="text-sm font-medium text-gray-700">Catatan:</span>
                                            <p class="text-sm text-gray-600 mt-1">{{ $pembayaran->catatan }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex gap-2 ml-4">
                                    <a href="{{ route('student.pembayaran.show', $pembayaran) }}"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm transition-colors">
                                        Detail
                                    </a>
                                    @if ($pembayaran->bukti_transfer)
                                        <a href="{{ route('student.pembayaran.download', $pembayaran) }}"
                                            class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded text-sm transition-colors">
                                            Download
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-soft p-8 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 text-lg mb-4">Belum ada pembayaran untuk tagihan ini</p>
                @if ($tagihan->status === 'belum_bayar')
                    <a href="{{ route('student.tagihan.pay', $tagihan) }}"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Bayar Sekarang
                    </a>
                @endif
            </div>
        @endif

        <!-- Info Bank untuk Transfer -->
        @if ($tagihan->status === 'belum_bayar')
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h4 class="font-medium text-blue-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informasi Transfer
                </h4>
                <div class="text-sm text-blue-800 space-y-2">
                    <p><strong>Bank BCA:</strong> 1234567890 a.n. MA Modern Miftahussa'adah</p>
                    <p><strong>Bank Mandiri:</strong> 9876543210 a.n. MA Modern Miftahussa'adah</p>
                    <p><strong>Bank BNI:</strong> 5555666777 a.n. MA Modern Miftahussa'adah</p>
                    <p class="mt-3 text-blue-700">
                        Silakan transfer sesuai nominal yang tertera dan upload bukti transfer melalui tombol "Bayar
                        Sekarang".
                    </p>
                </div>
            </div>
        @endif
    </div>
@endsection
