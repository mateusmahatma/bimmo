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

    <h3>Cash Flow</h3>

    <table>
        <tr>
            <td><strong>Total Income</strong></td>
            <td class="text-end">
                {{ number_format((float)$totalPemasukan) }}
            </td>
        </tr>
        <tr>
            <td><strong>Total Expense</strong></td>
            <td class="text-end">
                {{ number_format((float)$totalPengeluaran) }}
            </td>
        </tr>
        <tr>
            <td><strong>Net Balance</strong></td>
            <td class="text-end">
                {{ number_format((float)$netIncome) }}
            </td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Income Category</th>
                <th>Income Amount</th>
                <th>Expense Category</th>
                <th>Expense Amount</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row->tgl_transaksi }}</td>
                <td>{{ $row->pemasukanRelation?->nama ?? '-' }}</td>
                <td class="text-end">
                    {{ number_format((float)$row->nominal_pemasukan) }}
                </td>
                <td>{{ $row->pengeluaranRelation?->nama ?? '-' }}</td>
                <td class="text-end">
                    {{ number_format((float)$row->nominal) }}
                </td>
                @php
                    $desc = $row->keterangan ?? '-';
                    if ($row->keterangan) {
                        // Convert ordered lists: number each <li> inside <ol>
                        $desc = preg_replace_callback('/<ol[^>]*>(.*?)<\/ol>/is', function($m) {
                            $i = 1;
                            return preg_replace_callback('/<li[^>]*>(.*?)<\/li>/is', function($li) use (&$i) {
                                return ($i++) . '. ' . strip_tags($li[1]) . '<br>';
                            }, $m[1]);
                        }, $desc);
                        // Convert unordered lists: bullet each <li>
                        $desc = preg_replace_callback('/<ul[^>]*>(.*?)<\/ul>/is', function($m) {
                            return preg_replace_callback('/<li[^>]*>(.*?)<\/li>/is', function($li) {
                                return '- ' . strip_tags($li[1]) . '<br>';
                            }, $m[1]);
                        }, $desc);
                        // Convert remaining block elements to <br>
                        $desc = preg_replace('/<\/?(p|br|div|h[1-6])[^>]*>/i', '<br>', $desc);
                        $desc = trim(strip_tags($desc, '<br>'));
                    }
                @endphp
                <td>{!! $desc !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>