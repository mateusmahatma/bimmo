<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-end {
            text-align: right;
        }
    </style>
</head>

<body>

    <h3>Arus Kas</h3>

    <table>
        <tr>
            <td><strong>Total Pendapatan</strong></td>
            <td class="text-end">
                {{ number_format($totalPemasukan,0,',','.') }}
            </td>
        </tr>
        <tr>
            <td><strong>Total Pengeluaran</strong></td>
            <td class="text-end">
                {{ number_format($totalPengeluaran,0,',','.') }}
            </td>
        </tr>
        <tr>
            <td><strong>Laba Bersih</strong></td>
            <td class="text-end">
                {{ number_format($netIncome,0,',','.') }}
            </td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pemasukan</th>
                <th>Nominal Masuk</th>
                <th>Pengeluaran</th>
                <th>Nominal Keluar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $row)
            <tr>
                <td>{{ $row->tgl_transaksi }}</td>
                <td>{{ $row->pemasukanRelation?->nama ?? '-' }}</td>
                <td class="text-end">
                    {{ number_format($row->nominal_pemasukan,0,',','.') }}
                </td>
                <td>{{ $row->pengeluaranRelation?->nama ?? '-' }}</td>
                <td class="text-end">
                    {{ number_format($row->nominal,0,',','.') }}
                </td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>