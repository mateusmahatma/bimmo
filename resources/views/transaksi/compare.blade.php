<head>
    <title>Compare</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Data Transaksi</h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item">Manajemen Keuangan</li>
        <li class="breadcrumb-item active">Data Transaksi</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header mb-3">
        <ul class="nav nav-underline">
            <li class="nav-item">
                <a class="nav-link" href="/transaksi">Transaksi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/compare">Compare Pengeluaran</a>
            </li>
        </ul>
        <form id="compareForm">
            <div class="form-row align-items-center d-flex">
                <div class="col mx-3">
                    <p class="filter">Tanggal Pengeluaran Periode 1</p>
                    <div id="daterange" class="daterange">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span style="font-weight: bold;"></span>
                        <i class="fa fa-caret-down"></i>
                    </div>
                    <input type="hidden" id="start_date_1" name="start_date_1">
                    <input type="hidden" id="end_date_1" name="end_date_1">
                </div>
                <div class="col mx-3">
                    <p class="filter">Tanggal Pengeluaran Periode 2</p>
                    <div id="daterange2" class="daterange">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span style="font-weight: bold;"></span>
                        <i class="fa fa-caret-down"></i>
                    </div>
                    <input type="hidden" id="start_date_2" name="start_date_2">
                    <input type="hidden" id="end_date_2" name="end_date_2">
                </div>
                <div class="col mx-3">
                    <p class="filter">Jenis Pengeluaran</p>
                    <select class="form-control" name="filter_pengeluaran" id="pengeluaran">
                        <option value="">- Pilih -</option>
                        @foreach ($pengeluaran as $pengeluaran)
                        <option value="{{ $pengeluaran->nama }}">{{ $pengeluaran->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col mx-3">
                <button type="submit" class="btn-sm btn-color tombol-compare">Compare</button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="alert alert-info" role="alert">
            <strong>Informasi :</strong>
            <br>GAP = Nominal Pengeluaran Periode 1 - Nominal Pengeluaran Periode 2.</br>
        </div>
        <table class="customTable" id="comparisonTable">
            <thead>
                <tr>
                    <th class="text-center">Nominal Pengeluaran Periode 1</th>
                    <th class="text-center">Nominal Pengeluaran Periode 2</th>
                    <th class="text-center">GAP</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3" class="text-center">Belum ada data</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/compare.js') }}"></script>
@endsection