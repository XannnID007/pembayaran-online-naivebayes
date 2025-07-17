@extends('layouts.app')

@section('title', 'Input Pembayaran Manual')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Input Pembayaran Manual</h1>
            <a href="{{ route('admin.pembayaran.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <form action="{{ route('admin.pembayaran.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tagihan -->
                    <div class="md:col-span-2">
                        <label for="tagihan_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Tagihan</label>
                        <select id="tagihan_id" name="tagihan_id" required onchange="updateTagihanInfo()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tagihan_id') border-red-500 @enderror">
                            <option value="">Pilih tagihan yang belum dibayar</option>
                            @foreach ($tagihan as $t)
                                <option value="{{ $t->id }}" data-nominal="{{ $t->nominal }}"
                                    data-siswa="{{ $t->siswa->nama }}"
                                    data-jenis="{{ $t->jenisPembayaran->nama_pembayaran }}"
                                    data-periode="@if ($t->bulan) {{ \Carbon\Carbon::create()->month($t->bulan)->format('F') }} {{ $t->tahun }}@else{{ ucfirst($t->semester) }} {{ $t->tahun }} @endif"
                                    {{ old('tagihan_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->siswa->nama }} - {{ $t->jenisPembayaran->nama_pembayaran }}
                                    (@if ($t->bulan)
                                        {{ \Carbon\Carbon::create()->month($t->bulan)->format('F') }}
                                        {{ $t->tahun }}@else{{ ucfirst($t->semester) }} {{ $t->tahun }}
                                    @endif)
                                    - Rp {{ number_format($t->nominal, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('tagihan_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Tagihan -->
                    <div id="tagihan_info" class="md:col-span-2 bg-gray-50 rounded-lg p-4" style="display: none;">
                        <h4 class="font-medium text-gray-900 mb-2">Info Tagihan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Siswa:</span>
                                <span id="info_siswa" class="font-medium text-gray-900"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Jenis:</span>
                                <span id="info_jenis" class="font-medium text-gray-900"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Periode:</span>
                                <span id="info_periode" class="font-medium text-gray-900"></span>
                            </div>
                        </div>
                    </div>

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
                        <input type="number" id="jumlah_bayar" name="jumlah_bayar" value="{{ old('jumlah_bayar') }}"
                            required min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('jumlah_bayar') border-red-500 @enderror">
                        @error('jumlah_bayar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Metode Pembayaran -->
                    <div>
                        <label for="metode" class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                        <select id="metode" name="metode" required
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

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.pembayaran.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateTagihanInfo() {
            const select = document.getElementById('tagihan_id');
            const selectedOption = select.options[select.selectedIndex];
            const infoDiv = document.getElementById('tagihan_info');
            const jumlahBayarInput = document.getElementById('jumlah_bayar');

            if (selectedOption.value) {
                // Show info
                infoDiv.style.display = 'block';

                // Update info text
                document.getElementById('info_siswa').textContent = selectedOption.dataset.siswa;
                document.getElementById('info_jenis').textContent = selectedOption.dataset.jenis;
                document.getElementById('info_periode').textContent = selectedOption.dataset.periode;

                // Set jumlah bayar
                jumlahBayarInput.value = selectedOption.dataset.nominal;
                jumlahBayarInput.max = selectedOption.dataset.nominal;
            } else {
                // Hide info
                infoDiv.style.display = 'none';
                jumlahBayarInput.value = '';
                jumlahBayarInput.max = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTagihanInfo();
        });
    </script>
@endsection
