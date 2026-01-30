<h3>Arus Kas</h3>
<p>Total Pendapatan: Rp {{ number_format($totalPemasukan,0,',','.') }}</p>
<p>Total Pengeluaran: Rp {{ number_format($totalPengeluaran,0,',','.') }}</p>
<p>Laba Bersih: Rp {{ number_format($netIncome,0,',','.') }}</p>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Pemasukan</th>
            <th>Masuk</th>
            <th>Pengeluaran</th>
            <th>Keluar</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaksi as $row)
        <tr>
            <td>{{ $row->tgl_transaksi }}</td>
            <td>{{ $row->pemasukanRelation?->nama }}</td>
            <td>{{ number_format($row->nominal_pemasukan,0,',','.') }}</td>
            <td>{{ $row->pengeluaranRelation?->nama }}</td>
            <td>{{ number_format($row->nominal,0,',','.') }}</td>
            <td>{{ $row->keterangan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>