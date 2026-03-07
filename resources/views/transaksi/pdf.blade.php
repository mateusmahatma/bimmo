<!DOCTYPE html>
<html lang="id">

<head>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10px;
        }
    </style>

    <style>
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .header img {
            height: 50px;
            margin-right: 10px;
        }

        .header .brand {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        p {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 16px;
            color: #666;
        }

        /* kolom keterangan */
        .table-static {
            width: 100%;
            border-collapse: collapse;
            color: #000 !important;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .table-static td {
            border: 1px solid #ddd;
            padding: 0.4em 0.6em;
            vertical-align: top;
        }

        .table-static td:first-child {
            text-align: center;
            width: 25px;
        }
    </style>
</head>

<body>

    <table style="width: 100%; border:none; border-collapse: collapse;">
        <tr>
            <td style="width: 50px; vertical-align: middle; border: none;">
                <img src="{{ public_path('img/bimmo_icon.png') }}" alt="Logo" style="height: 45px;">
            </td>
            <td style="vertical-align: middle; padding-left: 8px; border: none;">
                <span style="display: block; font-weight: bold; font-size: 20px;">BIMMO</span>
                <span style="display: block; font-size: 10px; color: #555;">Budgeting Investment Money Movement</span>
            </td>
        </tr>
    </table>

    <center>
        <h1>Laporan Arus Kas</h1>
        <p>
            Periode:
            {{ \Carbon\Carbon::parse(request('start_date', $start_date ?? ''))->locale('id')->isoFormat('D MMMM Y') }}
            -
            {{ \Carbon\Carbon::parse(request('end_date', $end_date ?? ''))->locale('id')->isoFormat('D MMMM Y') }}
        </p>

        <div style="margin-bottom: 20px; font-size: 12px;">
            <strong>Total Pemasukan:</strong> Rp {{ number_format((float)($totalPemasukan ?? 0), 0, ',', '.') }} |
            <strong>Total Pengeluaran:</strong> Rp {{ number_format((float)($totalPengeluaran ?? 0), 0, ',', '.') }} |
            <strong>Saldo Bersih:</strong> Rp {{ number_format((float)($netIncome ?? 0), 0, ',', '.') }}
        </div>
    </center>

    <table class='table'>
        <thead>
            <tr>
                <th>No</th>
                <th>{{ __('Transaction Date') }}</th>
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
                <td>{{ $trans->pemasukanRelation->nama ?? '-' }}</td>
                <td>{{ number_format((float)$trans->nominal_pemasukan, 0, ',', '.') }}</td>
                <td>{{ $trans->pengeluaranRelation->nama ?? '-' }}</td>
                <td>{{ number_format((float)$trans->nominal, 0, ',', '.') }}</td>
                <td>
                    @if($trans->keterangan)
                        @php
                            $lines = array_filter(preg_split('/\r\n|\r|\n/', $trans->keterangan), function ($l) {
                                return trim($l) !== '';
                            });
                        @endphp
                        @if(count($lines) > 1)
                            <table style="width: 100%; border: none; border-collapse: collapse; margin: 0; padding: 0;">
                                @foreach($lines as $line)
                                    <tr>
                                        <td style="width: 15px; border: none; padding: 0 4px 2px 0; vertical-align: top; font-weight: bold; color: #666; font-size: 9px;">
                                            {{ $loop->iteration }}.
                                        </td>
                                        <td style="border: none; padding: 0 0 2px 0; vertical-align: top; font-size: 10px;">
                                            {{ trim($line) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            {{ $trans->keterangan }}
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>