<head>
    <title>Hasil Perhitungan Anggaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@extends('layouts.main')
@section('container')
<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Hasil Perhitungan Anggaran</h1>
    <div class="btn-group">
        <button class="btn-sm btn-color" id="downloadButton" onclick="downloadPDF()">
            <i class="fa fa-file-pdf-o"></i> Download PDF
        </button>
    </div>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item">Manajemen Keuangan</li>
        <li class="breadcrumb-item link"><a href="/kalkulator">Kalkulator Anggaran</a></li>
        <li class="breadcrumb-item active">Hasil Perhitungan</li>
    </ol>
</nav>
</div>

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