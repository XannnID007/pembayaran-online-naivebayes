@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Tambah Siswa Baru</h1>
            <a href="{{ route('admin.siswa.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-soft p-6">
            <form action="{{ route('admin.siswa.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('nama') border-red-500 @enderror">
                        @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIS -->
                    <div>
                        <label for="nis" class="block text-sm font-medium text-gray-700 mb-2">NIS</label>
                        <input type="text" id="nis" name="nis" value="{{ old('nis') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('nis') border-red-500 @enderror">
                        @error('nis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select id="kelas" name="kelas" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('kelas') border-red-500 @enderror">
                            <option value="">Pilih Kelas</option>
                            <option value="X IPA 1" {{ old('kelas') === 'X IPA 1' ? 'selected' : '' }}>X IPA 1</option>
                            <option value="X IPA 2" {{ old('kelas') === 'X IPA 2' ? 'selected' : '' }}>X IPA 2</option>
                            <option value="X IPS 1" {{ old('kelas') === 'X IPS 1' ? 'selected' : '' }}>X IPS 1</option>
                            <option value="X IPS 2" {{ old('kelas') === 'X IPS 2' ? 'selected' : '' }}>X IPS 2</option>
                            <option value="XI IPA 1" {{ old('kelas') === 'XI IPA 1' ? 'selected' : '' }}>XI IPA 1</option>
                            <option value="XI IPA 2" {{ old('kelas') === 'XI IPA 2' ? 'selected' : '' }}>XI IPA 2</option>
                            <option value="XI IPS 1" {{ old('kelas') === 'XI IPS 1' ? 'selected' : '' }}>XI IPS 1</option>
                            <option value="XI IPS 2" {{ old('kelas') === 'XI IPS 2' ? 'selected' : '' }}>XI IPS 2</option>
                            <option value="XII IPA 1" {{ old('kelas') === 'XII IPA 1' ? 'selected' : '' }}>XII IPA 1
                            </option>
                            <option value="XII IPA 2" {{ old('kelas') === 'XII IPA 2' ? 'selected' : '' }}>XII IPA 2
                            </option>
                            <option value="XII IPS 1" {{ old('kelas') === 'XII IPS 1' ? 'selected' : '' }}>XII IPS 1
                            </option>
                            <option value="XII IPS 2" {{ old('kelas') === 'XII IPS 2' ? 'selected' : '' }}>XII IPS 2
                            </option>
                        </select>
                        @error('kelas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('no_hp') border-red-500 @enderror">
                        @error('no_hp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('status') border-red-500 @enderror">
                            <option value="aktif" {{ old('status', $siswa->status) === 'aktif' ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="non_aktif"
                                {{ old('status', $siswa->status) === 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('alamat') border-red-500 @enderror">{{ old('alamat', $siswa->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.siswa.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Update Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
