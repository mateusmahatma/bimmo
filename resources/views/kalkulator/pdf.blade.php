<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table tr th,
        table tr td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table thead th {
            background-color: #f2f2f2;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>

<body>
    <center>
        <h1>Budget Calculation Results</h1>
    </center>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Income</td>
                <td>{{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
            @foreach ($budgetAllocations as $allocation)
            <tr>
                <td>{{ $allocation['nama_anggaran'] }} ({{ $allocation['persentase_anggaran'] }}%)</td>
                <td>{{ number_format($allocation['nominal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td>Total Budget</td>
                <td>{{ number_format($totalBudget, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Remaining Income</td>
                <td>{{ number_format($remainingIncome, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>