@extends('layouts.app')

@section('title', 'Bayar Tagihan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Bayar Tagihan</h1>
            <a href="{{ route('student.tagihan') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Info Tagihan -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Tagihan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <span class="text-sm font-medium text-gray-600">Jenis Pembayaran:</span>
                    <p class="text-lg font-semibold text-gray-900">{{ $tagihan->jenisPembayaran->nama_pembayaran }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-600">Nominal:</span>
                    <p class="text-2xl font-bold text-primary-600">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-600">Periode:</span>
                    <p class="text-lg text-gray-900">
                        @if ($tagihan->bulan)
                            {{ \Carbon\Carbon::create()->month($tagihan->bulan)->format('F') }} {{ $tagihan->tahun }}
                        @else
                            {{ ucfirst($tagihan->semester) }} {{ $tagihan->tahun }}
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-600">Deadline:</span>
                    <p class="text-lg text-gray-900">{{ $tagihan->deadline->format('d M Y') }}</p>
                    @if ($tagihan->is_terlambat)
                        <p class="text-sm text-red-600 font-medium">Terlambat {{ $tagihan->deadline->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Pembayaran -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Form Pembayaran</h3>

            <form action="{{ route('student.tagihan.store-pay', $tagihan) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal Bayar -->
                    <div>
                        <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                            Bayar</label>
                        <input type="date" id="tanggal_bayar" name="tanggal_bayar"
                            value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tanggal_bayar') border-red-500 @enderror">
                        @error('tanggal_bayar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Bayar -->
                    <div>
                        <label for="jumlah_bayar" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bayar</label>
                        <input type="number" id="jumlah_bayar" name="jumlah_bayar"
                            value="{{ old('jumlah_bayar', $tagihan->nominal) }}" required min="0"
                            max="{{ $tagihan->nominal }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('jumlah_bayar') border-red-500 @enderror">
                        @error('jumlah_bayar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Maksimal: Rp
                            {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div>
                        <label for="metode" class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                        <select id="metode" name="metode" required onchange="toggleBuktiTransfer()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('metode') border-red-500 @enderror">
                            <option value="">Pilih Metode</option>
                            <option value="tunai" {{ old('metode') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ old('metode') === 'transfer' ? 'selected' : '' }}>Transfer Bank
                            </option>
                        </select>
                        @error('metode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bukti Transfer -->
                    <div id="bukti_transfer_section" style="display: none;">
                        <label for="bukti_transfer" class="block text-sm font-medium text-gray-700 mb-2">Bukti
                            Transfer</label>
                        <input type="file" id="bukti_transfer" name="bukti_transfer" accept="image/*,.pdf"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('bukti_transfer') border-red-500 @enderror">
                        @error('bukti_transfer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, PDF. Maksimal 2MB</p>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan untuk pembayaran ini..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Bank -->
                <div id="info_bank" class="bg-blue-50 border border-blue-200 rounded-lg p-4" style="display: none;">
                    <h4 class="font-medium text-blue-900 mb-2">Informasi Transfer</h4>
                    <div class="text-sm text-blue-800">
                        <p><strong>Bank BCA:</strong> 1234567890 a.n. MA Modern Miftahussa'adah</p>
                        <p><strong>Bank Mandiri:</strong> 9876543210 a.n. MA Modern Miftahussa'adah</p>
                        <p class="mt-2 text-blue-700">Silakan transfer sesuai nominal yang tertera dan upload bukti
                            transfer.</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('student.tagihan') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Submit Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleBuktiTransfer() {
            const metode = document.getElementById('metode').value;
            const buktiSection = document.getElementById('bukti_transfer_section');
            const buktiInput = document.getElementById('bukti_transfer');
            const infoBank = document.getElementById('info_bank');

            if (metode === 'transfer') {
                buktiSection.style.display = 'block';
                buktiInput.required = true;
                infoBank.style.display = 'block';
            } else {
                buktiSection.style.display = 'none';
                buktiInput.required = false;
                infoBank.style.display = 'none';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleBuktiTransfer();
        });
    </script>
@endsection
