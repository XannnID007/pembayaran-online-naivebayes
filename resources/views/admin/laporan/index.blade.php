<div>
    <!-- Simplicity is an acquired taste. - Katharine Gerould -->
</div>
@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Laporan Keuangan</h1>
            <div class="flex gap-2">
                <button onclick="exportExcel()"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </button>
                <button onclick="exportPDF()"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ $filter['tanggal_mulai'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ $filter['tanggal_selesai'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pembayaran</label>
                    <select name="jenis_pembayaran_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Jenis</option>
                        @foreach ($jenisPembayaran as $jenis)
                            <option value="{{ $jenis->id }}"
                                {{ $filter['jenis_pembayaran_id'] == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama_pembayaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-soft p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Pembayaran</p>
                        <p class="text-xl font-bold text-gray-900">Rp
                            {{ number_format($summary['total_pembayaran'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

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
                        <p class="text-xl font-bold text-gray-900">Rp
                            {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</p>
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
                        <p class="text-sm text-gray-600">Pending Konfirmasi</p>
                        <p class="text-xl font-bold text-gray-900">{{ $summary['pembayaran_pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-soft p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Siswa Belum Bayar</p>
                        <p class="text-xl font-bold text-gray-900">{{ $summary['siswa_belum_bayar'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Reports -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pembayaran per Jenis -->
            <div class="bg-white rounded-xl shadow-soft p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pembayaran per Jenis</h3>
                <div class="space-y-3">
                    @foreach ($pembayaranPerJenis as $jenis)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $jenis->nama_pembayaran }}</span>
                            <span class="text-sm font-medium text-gray-900">Rp
                                {{ number_format($jenis->total, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Pembayar -->
            <div class="bg-white rounded-xl shadow-soft p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Pembayar</h3>
                <div class="space-y-3">
                    @foreach ($topPembayar as $pembayar)
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $pembayar->nama }}</div>
                                <div class="text-xs text-gray-500">{{ $pembayar->nis }} - {{ $pembayar->kelas }}</div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Rp
                                {{ number_format($pembayar->total, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tunggakan Terbesar -->
        @if ($tunggakanTerbesar->count() > 0)
            <div class="bg-white rounded-xl shadow-soft p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tunggakan Terbesar</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nominal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($tunggakanTerbesar as $tunggakan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $tunggakan->nama }}</div>
                                            <div class="text-sm text-gray-500">{{ $tunggakan->nis }} -
                                                {{ $tunggakan->kelas }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $tunggakan->nama_pembayaran }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($tunggakan->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        {{ \Carbon\Carbon::parse($tunggakan->deadline)->format('d M Y') }}
                                        ({{ \Carbon\Carbon::parse($tunggakan->deadline)->diffForHumans() }})
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script>
        function exportExcel() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = `/admin/laporan/export/excel?${params.toString()}`;
        }

        function exportPDF() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = `/admin/laporan/export/pdf?${params.toString()}`;
        }
    </script>
@endsection
