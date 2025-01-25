<!DOCTYPE html>
<html>

<head>
    <link href="/img/icon_pointech.png" rel="icon" />
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            /* Garis hitam */
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            /* Warna latar belakang untuk header */
        }
    </style>
</head>

<body>

    <center>
        <h1>Laporan Transaksi</h1>
        <p>Periode: {{ $start_date }} - {{ $end_date }}</p>
    </center>

    <table class='table'>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Transaksi</th>
                <th>Pemasukan</th>
                <th>Nominal Pemasukan</th>
                <th>Pengeluaran</th>
                <th>Nominal Pengeluaran</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1 @endphp
            @foreach ($transaksi as $trans)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ \Carbon\Carbon::parse($trans->tgl_transaksi)->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
                <td>{{ $trans->pemasukan }}</td>
                <td>{{ number_format($trans->nominal_pemasukan, 0, ',', '.') }}</td>
                <td>{{ $trans->pengeluaran }}</td>
                <td>{{ number_format($trans->nominal, 0, ',', '.') }}</td>
                <td>{{ $trans->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>