@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Edit Tagihan</h1>
            <a href="{{ route('admin.tagihan.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <form action="{{ route('admin.tagihan.update', $tagihan) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Siswa -->
                    <div>
                        <label for="siswa_id" class="block text-sm font-medium text-gray-700 mb-2">Siswa</label>
                        <select id="siswa_id" name="siswa_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('siswa_id') border-red-500 @enderror">
                            <option value="">Pilih Siswa</option>
                            @foreach ($siswa as $s)
                                <option value="{{ $s->id }}"
                                    {{ old('siswa_id', $tagihan->siswa_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }} ({{ $s->nis }}) - {{ $s->kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('siswa_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Pembayaran -->
                    <div>
                        <label for="jenis_pembayaran_id" class="block text-sm font-medium text-gray-700 mb-2">Jenis
                            Pembayaran</label>
                        <select id="jenis_pembayaran_id" name="jenis_pembayaran_id" required onchange="updateNominal()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('jenis_pembayaran_id') border-red-500 @enderror">
                            <option value="">Pilih Jenis Pembayaran</option>
                            @foreach ($jenisPembayaran as $jenis)
                                <option value="{{ $jenis->id }}" data-nominal="{{ $jenis->nominal }}"
                                    data-periode="{{ $jenis->periode }}"
                                    {{ old('jenis_pembayaran_id', $tagihan->jenis_pembayaran_id) == $jenis->id ? 'selected' : '' }}>
                                    {{ $jenis->nama_pembayaran }} - Rp {{ number_format($jenis->nominal, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_pembayaran_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Periode Fields -->
                    <div id="bulan_field"
                        style="display: {{ $tagihan->jenisPembayaran->periode === 'bulanan' ? 'block' : 'none' }};">
                        <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select id="bulan" name="bulan"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('bulan') border-red-500 @enderror">
                            <option value="">Pilih Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ sprintf('%02d', $i) }}"
                                    {{ old('bulan', $tagihan->bulan) == sprintf('%02d', $i) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                        @error('bulan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="semester_field"
                        style="display: {{ $tagihan->jenisPembayaran->periode === 'semester' ? 'block' : 'none' }};">
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                        <select id="semester" name="semester"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('semester') border-red-500 @enderror">
                            <option value="">Pilih Semester</option>
                            <option value="ganjil"
                                {{ old('semester', $tagihan->semester) === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ old('semester', $tagihan->semester) === 'genap' ? 'selected' : '' }}>
                                Genap</option>
                        </select>
                        @error('semester')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun -->
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <input type="number" id="tahun" name="tahun" value="{{ old('tahun', $tagihan->tahun) }}"
                            required min="2020" max="2030"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tahun') border-red-500 @enderror">
                        @error('tahun')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nominal -->
                    <div>
                        <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">Nominal</label>
                        <input type="number" id="nominal" name="nominal" value="{{ old('nominal', $tagihan->nominal) }}"
                            required min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('nominal') border-red-500 @enderror">
                        @error('nominal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                        <input type="date" id="deadline" name="deadline"
                            value="{{ old('deadline', $tagihan->deadline->format('Y-m-d')) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('deadline') border-red-500 @enderror">
                        @error('deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('status') border-red-500 @enderror">
                            <option value="belum_bayar"
                                {{ old('status', $tagihan->status) === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar
                            </option>
                            <option value="sudah_bayar"
                                {{ old('status', $tagihan->status) === 'sudah_bayar' ? 'selected' : '' }}>Sudah Bayar
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.tagihan.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Update Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateNominal() {
            const select = document.getElementById('jenis_pembayaran_id');
            const nominalInput = document.getElementById('nominal');
            const bulanField = document.getElementById('bulan_field');
            const semesterField = document.getElementById('semester_field');
            const bulanInput = document.getElementById('bulan');
            const semesterInput = document.getElementById('semester');

            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                // Set nominal
                nominalInput.value = selectedOption.dataset.nominal;

                // Show/hide periode fields
                if (selectedOption.dataset.periode === 'bulanan') {
                    bulanField.style.display = 'block';
                    semesterField.style.display = 'none';
                    bulanInput.required = true;
                    semesterInput.required = false;
                    semesterInput.value = '';
                } else if (selectedOption.dataset.periode === 'semester') {
                    bulanField.style.display = 'none';
                    semesterField.style.display = 'block';
                    bulanInput.required = false;
                    semesterInput.required = true;
                    bulanInput.value = '';
                }
            } else {
                nominalInput.value = '';
                bulanField.style.display = 'none';
                semesterField.style.display = 'none';
                bulanInput.required = false;
                semesterInput.required = false;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNominal();
        });
    </script>
@endsection
