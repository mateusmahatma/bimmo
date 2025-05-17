<head>
    <title>Hasil Perhitungan Anggaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Hasil Perhitungan Anggaran</a>
    <ul class="nav nav-pills">
        <li class="nav-item dropdown">
            <a class="nav-link" href="/kalkulator" role="button" aria-expanded="false">
                <span class="badge-primary">Kalkulator Anggaran</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" role="button" id="downloadButton" onclick="downloadPDF()">
                <span class="badge-pdf">Download PDF</span>
            </a>
        </li>
    </ul>
</nav>

<div class="card">
    <div class="card-header mb-3">
    </div>
    <div class="card-body">
        <table class="customTable">
            <thead>
                <tr>
                    <th class="text-center">Kategori</th>
                    <th class="text-center">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Pendapatan</td>
                    <td>{{ number_format($totalIncome, 0, ',', '.') }}</td>
                </tr>
                @foreach ($budgetAllocations as $allocation)
                <tr>
                    <td>{{ $allocation['nama_anggaran'] }} ({{ $allocation['persentase_anggaran'] }}%)</td>
                    <td>{{ number_format($allocation['nominal'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td>Total Anggaran</td>
                    <td>{{ number_format($totalBudget, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Sisa Pendapatan</td>
                    <td>{{ number_format($remainingIncome, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src=" {{ asset('js/kalkulator.js') }}">
</script>
@endsection