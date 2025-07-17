@extends('layouts.app')

@section('title', 'Edit Jenis Pembayaran')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Edit Jenis Pembayaran: {{ $jenisPembayaran->nama_pembayaran }}</h1>
            <a href="{{ route('admin.jenis-pembayaran.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <form action="{{ route('admin.jenis-pembayaran.update', $jenisPembayaran) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Pembayaran -->
                    <div>
                        <label for="nama_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Nama
                            Pembayaran</label>
                        <input type="text" id="nama_pembayaran" name="nama_pembayaran"
                            value="{{ old('nama_pembayaran', $jenisPembayaran->nama_pembayaran) }}" required
                            class="w-full px-3 py-2 border {{ $errors->has('nama_pembayaran') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        @error('nama_pembayaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nominal -->
                    <div>
                        <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">Nominal (Rp)</label>
                        <input type="number" id="nominal" name="nominal"
                            value="{{ old('nominal', $jenisPembayaran->nominal) }}" required min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('nominal') border-red-500 @enderror">
                        @error('nominal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Periode -->
                    <div>
                        <label for="periode" class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                        <select id="periode" name="periode" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('periode') border-red-500 @enderror">
                            <option value="bulanan"
                                {{ old('periode', $jenisPembayaran->periode) === 'bulanan' ? 'selected' : '' }}>Bulanan
                            </option>
                            <option value="semester"
                                {{ old('periode', $jenisPembayaran->periode) === 'semester' ? 'selected' : '' }}>Semester
                            </option>
                        </select>
                        @error('periode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label for="aktif" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="aktif" name="aktif"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('aktif') border-red-500 @enderror">
                            <option value="1"
                                {{ old('aktif', $jenisPembayaran->aktif ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="0"
                                {{ old('aktif', $jenisPembayaran->aktif ? '1' : '0') === '0' ? 'selected' : '' }}>Non-Aktif
                            </option>
                        </select>
                        @error('aktif')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $jenisPembayaran->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.jenis-pembayaran.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
