<table>
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
                <td style="text-align: right;">{{ $p->jumlah_bayar }}</td>
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
                <td colspan="7" style="text-align: right; font-weight: bold;">TOTAL PEMASUKAN</td>
                <td style="text-align: right; font-weight: bold;">{{ $total }}</td>
            </tr>
            <tr>
                <td colspan="7" style="text-align: right; font-weight: bold;">JUMLAH TRANSAKSI</td>
                <td style="text-align: right; font-weight: bold;">{{ count($pembayaran) }}</td>
            </tr>
        </tfoot>
    @endif
</table>
