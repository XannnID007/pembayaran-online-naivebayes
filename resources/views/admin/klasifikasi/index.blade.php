@extends('layouts.app')

@section('title', 'Klasifikasi Siswa - Naive Bayes')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Klasifikasi Pola Pembayaran Siswa</h1>
            <button onclick="runClassification()"
                class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Jalankan Klasifikasi
            </button>
        </div>

        <!-- Algorithm Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-lg font-medium text-blue-900 mb-2">Algoritma Naive Bayes</h4>
                    <p class="text-blue-800 text-sm">
                        Sistem menggunakan algoritma Naive Bayes untuk mengklasifikasikan pola pembayaran siswa berdasarkan
                        data historis.
                        Klasifikasi dilakukan berdasarkan ketepatan waktu, frekuensi bayar, jenis pembayaran yang dipilih,
                        dan tingkat kelas siswa.
                    </p>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-green-100 text-green-800 px-3 py-2 rounded">
                            <strong>Pembayar Disiplin:</strong> Selalu bayar tepat waktu untuk semua jenis
                        </div>
                        <div class="bg-red-100 text-red-800 px-3 py-2 rounded">
                            <strong>Pembayar Terlambat:</strong> Sering bayar setelah deadline
                        </div>
                        <div class="bg-yellow-100 text-yellow-800 px-3 py-2 rounded">
                            <strong>Pembayar Selektif:</strong> Hanya bayar jenis pembayaran tertentu
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-soft p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Diklasifikasi</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $report['total_classified'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Siswa aktif</p>
            </div>

            <div class="bg-white rounded-xl shadow-soft p-6">
                <h3 class="text-sm font-medium text-gray-500">Pembayar Disiplin</h3>
                <p class="text-2xl font-bold text-green-600">{{ $report['distribution']['pembayar_disiplin']['count'] }}</p>
                <p class="text-sm text-gray-500">{{ $report['distribution']['pembayar_disiplin']['percentage'] }}% dari
                    total</p>
            </div>

            <div class="bg-white rounded-xl shadow-soft p-6">
                <h3 class="text-sm font-medium text-gray-500">Pembayar Terlambat</h3>
                <p class="text-2xl font-bold text-red-600">{{ $report['distribution']['pembayar_terlambat']['count'] }}</p>
                <p class="text-sm text-gray-500">{{ $report['distribution']['pembayar_terlambat']['percentage'] }}% dari
                    total</p>
            </div>

            <div class="bg-white rounded-xl shadow-soft p-6">
                <h3 class="text-sm font-medium text-gray-500">Pembayar Selektif</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ $report['distribution']['pembayar_selektif']['count'] }}
                </p>
                <p class="text-sm text-gray-500">{{ $report['distribution']['pembayar_selektif']['percentage'] }}% dari
                    total</p>
            </div>
        </div>

        <!-- Classification Results -->
        <div class="bg-white rounded-xl shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Hasil Klasifikasi Terbaru</h3>
                <p class="text-sm text-gray-600 mt-1">Confidence Score:
                    {{ number_format($report['average_confidence'] * 100, 1) }}% rata-rata</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori Prediksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Confidence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Prediksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($klasifikasi as $k)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                            <span
                                                class="text-sm font-medium text-primary-600">{{ substr($k->siswa->nama, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $k->siswa->nama }}</div>
                                            <div class="text-sm text-gray-500">{{ $k->siswa->nis }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $k->siswa->kelas }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $k->kategori_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-primary-500 h-2 rounded-full"
                                                style="width: {{ $k->confidence_score * 100 }}%"></div>
                                        </div>
                                        <span
                                            class="text-sm text-gray-900">{{ number_format($k->confidence_score * 100, 1) }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $k->tanggal_prediksi->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="showDetails({{ $k->id }})"
                                        class="text-primary-600 hover:text-primary-900">Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Belum ada hasil klasifikasi. Klik tombol "Jalankan Klasifikasi" untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($klasifikasi->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $klasifikasi->links() }}
                </div>
            @endif
        </div>

        <!-- Insights & Recommendations -->
        @if ($report['total_classified'] > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-soft p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Insights</h3>
                    <div class="space-y-3">
                        @if ($report['distribution']['pembayar_disiplin']['percentage'] > 60)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-gray-700">Mayoritas siswa
                                    ({{ $report['distribution']['pembayar_disiplin']['percentage'] }}%) memiliki pola
                                    pembayaran yang disiplin.</p>
                            </div>
                        @endif

                        @if ($report['distribution']['pembayar_terlambat']['percentage'] > 20)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-gray-700">
                                    {{ $report['distribution']['pembayar_terlambat']['percentage'] }}% siswa memiliki pola
                                    pembayaran terlambat yang perlu perhatian khusus.</p>
                            </div>
                        @endif

                        @if ($report['distribution']['pembayar_selektif']['percentage'] > 15)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-gray-700">
                                    {{ $report['distribution']['pembayar_selektif']['percentage'] }}% siswa cenderung
                                    selektif dalam pembayaran.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-soft p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rekomendasi Tindakan</h3>
                    <div class="space-y-3">
                        <div class="border-l-4 border-green-400 pl-4">
                            <h4 class="font-medium text-green-800">Pembayar Disiplin</h4>
                            <p class="text-sm text-green-700">Berikan apresiasi dan jadikan contoh untuk siswa lain.</p>
                        </div>

                        <div class="border-l-4 border-red-400 pl-4">
                            <h4 class="font-medium text-red-800">Pembayar Terlambat</h4>
                            <p class="text-sm text-red-700">Lakukan pendekatan personal dan reminder lebih intensif sebelum
                                deadline.</p>
                        </div>

                        <div class="border-l-4 border-yellow-400 pl-4">
                            <h4 class="font-medium text-yellow-800">Pembayar Selektif</h4>
                            <p class="text-sm text-yellow-700">Berikan edukasi tentang pentingnya melengkapi semua jenis
                                pembayaran.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function runClassification() {
            if (confirm(
                    'Yakin ingin menjalankan klasifikasi? Proses ini akan memakan waktu beberapa menit dan akan memperbarui hasil klasifikasi yang ada.'
                    )) {
                // Show loading
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML =
                    '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
                button.disabled = true;

                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.klasifikasi.run') }}';

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';
                form.appendChild(token);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function showDetails(id) {
            // Implementation for showing classification details
            alert('Detail klasifikasi akan ditampilkan dalam modal (belum diimplementasi)');
        }
    </script>
@endsection
