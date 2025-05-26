<head>
    <title>Data Transaksi</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/transaksi">Data Transaksi</a>
    <ul class="nav nav-pills">
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <span class="badge-primary dropdown-toggle">Aksi</span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#transaksiModal">Tambah Data</a></li>
                <li><a class=" dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importExcelModal">Import Data</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadPDFTransaksi()">Download PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadExcel()">Download Excel</a></li>
                <li>
                    <hr class=" dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/compare">Compare Pengeluaran</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/compare">
                <span class="badge-orange">Compare Pengeluaran</span>
            </a>
        </li>
    </ul>
</nav>

@include('modal.transaksi.index')
@include('modal.transaksi.import_excel')
@include('modal.transaksi.upload')

<div class="card-header">
    <div class="card-header">
        <form>
            <div class="form-row align-items-center d-flex">
                <div class="col mx-3">
                    <p class="filter">Tanggal Transaksi</p>
                    <div id="daterange" class="daterange"> <i class="fa fa-calendar"></i>&nbsp;
                        <span></span>
                        <i class="fa fa-caret-down"></i>
                    </div>
                </div>
                <div class="col mx-3">
                    <p class="filter">Jenis Pemasukan</p>
                    <select class="form-control" name="filter_pemasukan">
                        <option value="">- Pilih -</option>
                        @foreach ($pemasukan as $pemasukan)
                        <option value="{{ $pemasukan->nama }}">{{ $pemasukan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col mx-3">
                    <p class="filter">Jenis Pengeluaran</p>
                    <select class="form-control" name="filter_pengeluaran">
                        <option value="">- Pilih -</option>
                        @foreach ($pengeluaran as $pengeluaran)
                        <option value="{{ $pengeluaran->nama }}">{{ $pengeluaran->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <table id="transaksiTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 3px;">No</th>
                    <th class="text-center">Tanggal Transaksi</th>
                    <th class="text-center">Pemasukan</th>
                    <th class="text-center">Nominal Pemasukan</th>
                    <th class="text-center">Pengeluaran</th>
                    <th class="text-center">Nominal Pengeluaran</th>
                    <th style="width: 200px;" class="text-center">Keterangan</th>
                    <th style="width: 60px;">Dibuat Tanggal</th>
                    <th style="width: 60px;">Diupdate Tanggal</th>
                    <th style="width: 3px;">Aksi</th>
                </tr>
            </thead>
        </table>
        <div class="badge-success" style="font-size: medium;">
            Total Pemasukan: <span id="totalPemasukan">0</span>
        </div>
        <div class="badge-danger" style="font-size: medium">
            Total Pengeluaran: <span id="totalPengeluaran">0</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/transaksi.js') }}?v={{ filemtime(public_path('js/transaksi.js')) }}"></script>
@endsection