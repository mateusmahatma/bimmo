<head>
    <title>Compare Pengeluaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/compare">Compare Pengeluaran</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link" href="/transaksi">
                <span class="badge-primary">Data Transaksi</span>
            </a>
        </li>
    </ul>
</nav>

<div class="card-header">
    <div class="card-header">
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
                <button type="submit" class="cssbuttons-io-button tombol-compare">Compare</button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="alert alert-info d-flex align-items-start gap-2" role="alert">
            <i class="bi bi-info-circle-fill fs-4"></i>
            <div>
                <strong>Informasi:</strong>
                <p class="mb-0">
                    GAP = Nominal Pengeluaran Periode 1 - Nominal Pengeluaran Periode 2.
                </p>
            </div>
        </div>
        <table id="comparisonTable" class="customTable">
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
<script src="{{ asset('js/compare.js') }}?v={{ filemtime(public_path('js/compare.js')) }}"></script>
@endsection