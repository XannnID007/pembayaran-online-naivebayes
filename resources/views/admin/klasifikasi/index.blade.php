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

    <div id="detailModal" x-data="{ open: false }" @keydown.window.escape="open = false" x-show="open"
        class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    @click.away="open = false"
                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-6">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                                    Detail Hasil Klasifikasi
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">Rincian analisis Naive Bayes untuk siswa.
                                </p>
                            </div>
                            <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-5 border-t border-gray-200">
                            <dl class="divide-y divide-gray-200" id="modalContent">
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            const modal = document.getElementById('detailModal');
            const modalContent = document.getElementById('modalContent');
            const alpineData = modal._x_dataStack[0];

            fetch(`/admin/klasifikasi/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Fungsi untuk membuat baris detail
                    const createDetailRow = (label, value) => {
                        return `<div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5"><dt class="text-sm font-medium text-gray-500">${label}</dt><dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">${value}</dd></div>`;
                    };

                    // Format kategori prediksi dengan badge
                    let kategori = data.kategori_prediksi.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    let badgeColor = 'bg-gray-100 text-gray-800';
                    if (kategori === 'Lancar') badgeColor = 'bg-green-100 text-green-800';
                    else if (kategori === 'Kurang Lancar') badgeColor = 'bg-yellow-100 text-yellow-800';
                    else if (kategori === 'Bermasalah') badgeColor = 'bg-red-100 text-red-800';
                    const kategoriBadge =
                        `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${badgeColor}">${kategori}</span>`;

                    // Format confidence score dengan progress bar
                    const confidencePercentage = (data.confidence_score * 100).toFixed(2);
                    const confidenceBar = `
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                            <div class="bg-primary-600 h-2.5 rounded-full" style="width: ${confidencePercentage}%"></div>
                        </div>
                        <span>${confidencePercentage}%</span>
                    </div>`;

                    // Fungsi untuk mem-format objek JSON menjadi HTML yang rapi
                    const formatJsonDetail = (obj) => {
                        let html =
                            '<dl class="divide-y divide-gray-100 border border-gray-200 rounded-md p-3 bg-gray-50">';
                        for (const key in obj) {
                            let value = obj[key];
                            let formattedValue = '';

                            if (typeof value === 'object' && value !== null) {
                                // Rekursif untuk objek di dalam objek
                                formattedValue = formatJsonDetail(value);
                            } else {
                                // Format nilai menjadi lebih mudah dibaca
                                if (typeof value === 'number') {
                                    formattedValue =
                                        `<span class="font-mono text-blue-600">${value.toFixed(6)}</span>`;
                                } else {
                                    formattedValue = `<span class="font-mono text-green-700">'${value}'</span>`;
                                }
                            }

                            html +=
                                `<div class="px-2 py-2 sm:grid sm:grid-cols-3 sm:gap-4"><dt class="text-xs font-medium text-gray-500">${key.replace(/_/g, ' ')}</dt><dd class="mt-1 text-xs text-gray-900 sm:col-span-2 sm:mt-0">${formattedValue}</dd></div>`;
                        }
                        html += '</dl>';
                        return html;
                    };

                    const detailAnalisisHtml = data.detail_analisis ? formatJsonDetail(data.detail_analisis) :
                        'Tidak ada detail.';

                    // Gabungkan semua HTML
                    modalContent.innerHTML = `
                    ${createDetailRow('Nama Siswa', data.siswa.nama)}
                    ${createDetailRow('NIS', data.siswa.nis)}
                    ${createDetailRow('Kelas', data.siswa.kelas)}
                    ${createDetailRow('Prediksi Kategori', kategoriBadge)}
                    ${createDetailRow('Tingkat Keyakinan', confidenceBar)}
                    ${createDetailRow('Rincian Analisis', `<div class="w-full overflow-x-auto">${detailAnalisisHtml}</div>`)}
                `;

                    // Buka modal menggunakan AlpineJS
                    alpineData.open = true;
                });
        }
    </script>
@endsection
