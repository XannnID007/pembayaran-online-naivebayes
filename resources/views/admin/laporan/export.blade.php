<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header-table .logo {
            width: 90px;
            text-align: left;
        }

        .header-table .logo img {
            width: 80px;
            height: 80px;
        }

        .header-table .title {
            text-align: center;
        }

        .header-table h1 {
            margin: 0;
            font-size: 22px;
        }

        .header-table p {
            margin: 2px 0 0;
            font-size: 12px;
        }

        .report-info {
            margin: 25px 0;
        }

        .report-info h2 {
            text-align: center;
            margin-bottom: 5px;
            font-size: 16px;
            text-decoration: underline;
        }

        .report-info p {
            text-align: center;
            margin-top: 0;
            font-size: 12px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .data-table thead {
            background-color: #f2f2f2;
        }

        .data-table tfoot {
            font-weight: bold;
        }

        .data-table tfoot td {
            background-color: #f9f9f9;
        }

        .footer {
            text-align: right;
            font-size: 10px;
            color: #777;
            position: fixed;
            bottom: -20px;
            width: 100%;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td class="logo">
                    {{-- Ganti path logo jika perlu --}}
                    <img src="{{ public_path('images/logo.jpeg') }}" alt="Logo Sekolah">
                </td>
                <td class="title">
                    <h1>MA MODERN MIFTAHUSSA'ADAH KOTA CIMAHI</h1>
                    <p>Jl. Cibabat No. 123, Kota Cimahi, Indonesia</p>
                    <p>Telepon: (021) 123456 | Email: info@miftahussaadah.sch.id</p>
                </td>
            </tr>
        </table>

        <div class="report-info">
            <h2>LAPORAN PEMBAYARAN SISWA</h2>
            <p>Periode: {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d M Y') }} -
                {{ \Carbon\Carbon::parse(request('tanggal_selesai'))->format('d M Y') }}</p>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Bayar</th>
                    <th>Siswa</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Jenis Pembayaran</th>
                    <th>Metode</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp
                @forelse($pembayaran as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $p->tanggal_bayar->format('d-m-Y H:i') }}</td>
                        <td>{{ $p->tagihan->siswa->nama }}</td>
                        <td>{{ $p->tagihan->siswa->nis }}</td>
                        <td>{{ $p->tagihan->siswa->kelas }}</td>
                        <td>{{ $p->tagihan->jenisPembayaran->nama_pembayaran }}</td>
                        <td>{{ $p->metode }}</td>
                        <td class="text-right">{{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $total += $p->jumlah_bayar;
                    @endphp
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">Tidak ada data untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
            @if (count($pembayaran) > 0)
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-right"><strong>TOTAL PEMASUKAN</strong></td>
                        <td class="text-right"><strong>{{ number_format($total, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="7" class="text-right"><strong>JUMLAH TRANSAKSI</strong></td>
                        <td class="text-right"><strong>{{ count($pembayaran) }}</strong></td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <div class="footer">
            Dicetak pada: {{ now()->format('d M Y, H:i:s') }}
        </div>
    </div>
</body>

</html>
