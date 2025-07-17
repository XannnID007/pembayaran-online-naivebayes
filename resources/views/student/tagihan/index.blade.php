@extends('layouts.app')

@section('title', 'Tagihan Saya')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Tagihan Saya</h1>
        </div>

        <!-- Summary Cards -->
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
                        <p class="text-xl font-bold text-gray-900">{{ $summary['total_tagihan'] }}</p>
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
                        <p class="text-xl font-bold text-gray-900">{{ $summary['belum_bayar'] }}</p>
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
                        <p class="text-xl font-bold text-gray-900">{{ $summary['sudah_bayar'] }}</p>
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
                        <p class="text-sm text-gray-600">Terlambat</p>
                        <p class="text-xl font-bold text-gray-900">{{ $summary['terlambat'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <select name="jenis"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Jenis</option>
                    <option value="SPP" {{ request('jenis') === 'SPP' ? 'selected' : '' }}>SPP</option>
                    <option value="UTS" {{ request('jenis') === 'UTS' ? 'selected' : '' }}>UTS</option>
                    <option value="UAS" {{ request('jenis') === 'UAS' ? 'selected' : '' }}>UAS</option>
                </select>

                <select name="status"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar
                    </option>
                    <option value="sudah_bayar" {{ request('status') === 'sudah_bayar' ? 'selected' : '' }}>Sudah Bayar
                    </option>
                </select>

                <button type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Filter
                </button>

                @if (request()->hasAny(['jenis', 'status']))
                    <a href="{{ route('student.tagihan') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Daftar Tagihan -->
        <div class="space-y-4">
            @forelse($tagihan as $t)
                <div class="bg-white rounded-xl shadow-soft p-6 {{ $t->is_terlambat ? 'border-l-4 border-red-500' : '' }}">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $t->jenisPembayaran->nama_pembayaran }}
                                </h3>
                                <div>{!! $t->status_badge !!}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">Periode:</span>
                                    @if ($t->bulan)
                                        {{ \Carbon\Carbon::create()->month($t->bulan)->format('F') }} {{ $t->tahun }}
                                    @else
                                        {{ ucfirst($t->semester) }} {{ $t->tahun }}
                                    @endif
                                </div>
                                <div>
                                    <span class="font-medium">Deadline:</span>
                                    {{ $t->deadline->format('d M Y') }}
                                    @if ($t->is_terlambat)
                                        <span class="text-red-600 font-medium">({{ $t->deadline->diffForHumans() }})</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-medium">Nominal:</span>
                                    <span class="text-lg font-bold text-gray-900">Rp
                                        {{ number_format($t->nominal, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            @if ($t->pembayaran->count() > 0)
                                <div class="mt-3">
                                    <span class="text-sm font-medium text-gray-700">Pembayaran:</span>
                                    @foreach ($t->pembayaran as $p)
                                        <div class="text-sm text-gray-600">
                                            {{ $p->tanggal_bayar->format('d M Y') }} - Rp
                                            {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                                            {!! $p->status_badge !!}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            @if ($t->status === 'belum_bayar')
                                <a href="{{ route('student.tagihan.pay', $t) }}"
                                    class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Bayar
                                </a>
                            @endif

                            <a href="{{ route('student.tagihan.show', $t) }}"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-soft p-8 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-gray-500">Tidak ada tagihan ditemukan</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($tagihan->hasPages())
            <div class="bg-white rounded-xl shadow-soft p-6">
                {{ $tagihan->links() }}
            </div>
        @endif

        <!-- Total Belum Bayar -->
        @if ($summary['total_nominal_belum_bayar'] > 0)
            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-red-900">Total Yang Belum Dibayar</h4>
                        <p class="text-2xl font-bold text-red-900">Rp
                            {{ number_format($summary['total_nominal_belum_bayar'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
