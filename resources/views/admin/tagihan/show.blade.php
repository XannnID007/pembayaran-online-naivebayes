@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Detail Tagihan</h1>
            <div class="flex gap-2">
                @if ($tagihan->status === 'belum_bayar')
                    <a href="{{ route('admin.tagihan.edit', $tagihan) }}"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                @endif
                <a href="{{ route('admin.tagihan.index') }}"
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
                    <p class="mt-1 text-2xl font-bold text-primary-600">Rp
                        {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Deadline</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $tagihan->deadline->format('d M Y') }}</p>
                    @if ($tagihan->is_terlambat)
                        <p class="text-sm text-red-600 font-medium">Terlambat {{ $tagihan->deadline->diffForHumans() }}</p>
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

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Bayar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Metode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dikonfirmasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($tagihan->pembayaran as $pembayaran)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $pembayaran->tanggal_bayar->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded capitalize">
                                            {{ $pembayaran->metode }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {!! $pembayaran->status_badge !!}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if ($pembayaran->confirmedBy)
                                            {{ $pembayaran->confirmedBy->name }}
                                            <div class="text-xs text-gray-500">
                                                {{ $pembayaran->updated_at->format('d M Y H:i') }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.pembayaran.show', $pembayaran) }}"
                                            class="text-primary-600 hover:text-primary-900">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-soft p-8 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 text-lg">Belum ada pembayaran untuk tagihan ini</p>
                @if ($tagihan->status === 'belum_bayar')
                    <p class="text-gray-400 text-sm mt-2">Siswa dapat melakukan pembayaran melalui portal online</p>
                @endif
            </div>
        @endif
    </div>
@endsection
