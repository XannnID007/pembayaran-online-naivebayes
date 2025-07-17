@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Detail Pembayaran</h1>
            <div class="flex gap-2">
                @if ($pembayaran->status_konfirmasi === 'pending')
                    <button onclick="confirmPayment({{ $pembayaran->id }})"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Konfirmasi
                    </button>
                    <button onclick="rejectPayment({{ $pembayaran->id }})"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Tolak
                    </button>
                @endif
                <a href="{{ route('admin.pembayaran.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Info Pembayaran -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Siswa</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $pembayaran->tagihan->siswa->nama }}</p>
                    <p class="text-sm text-gray-500">{{ $pembayaran->tagihan->siswa->nis }} -
                        {{ $pembayaran->tagihan->siswa->kelas }}</p>
                </div>
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
                    <label class="block text-sm font-medium text-gray-500">Jumlah Bayar</label>
                    <p class="mt-1 text-2xl font-bold text-primary-600">Rp
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
                    <label class="block text-sm font-medium text-gray-500">Status Konfirmasi</label>
                    <div class="mt-1">
                        {!! $pembayaran->status_badge !!}
                    </div>
                </div>
                @if ($pembayaran->confirmedBy)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Dikonfirmasi Oleh</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $pembayaran->confirmedBy->name }}</p>
                        <p class="text-sm text-gray-500">{{ $pembayaran->updated_at->format('d M Y H:i') }}</p>
                    </div>
                @endif
            </div>

            @if ($pembayaran->catatan)
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
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Bukti Transfer</p>
                            <p class="text-sm text-gray-500">{{ basename($pembayaran->bukti_transfer) }}</p>
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
                        <a href="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" download
                            class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download
                        </a>
                    </div>
                </div>

                <!-- Preview Image -->
                @if (in_array(strtolower(pathinfo($pembayaran->bukti_transfer, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                    <div class="mt-4">
                        <img src="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" alt="Bukti Transfer"
                            class="max-w-full h-auto max-h-96 mx-auto rounded-lg shadow-md">
                    </div>
                @endif
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
                        <p class="text-sm text-red-600">Terlambat {{ $pembayaran->tagihan->deadline->diffForHumans() }}
                        </p>
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
    </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Konfirmasi Pembayaran</h3>
                <p class="text-gray-600 mb-4">Yakin ingin mengkonfirmasi pembayaran ini?</p>
                <form id="confirmForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea name="catatan" rows="3" placeholder="Tambahkan catatan konfirmasi..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Pembayaran</h3>
                <p class="text-gray-600 mb-4">Pembayaran yang ditolak tidak dapat dikonfirmasi lagi.</p>
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span
                                class="text-red-500">*</span></label>
                        <textarea name="catatan" rows="3" required placeholder="Jelaskan alasan penolakan pembayaran..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmPayment(id) {
            document.getElementById('confirmForm').action = `/admin/pembayaran/${id}/confirm`;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function rejectPayment(id) {
            document.getElementById('rejectForm').action = `/admin/pembayaran/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
@endsection
