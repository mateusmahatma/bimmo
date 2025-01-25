<head>
    <title>Data Transaksi</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Data Transaksi</h1>
    <div class="btn-group">
        <button class="btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Action
        </button>
        <ul class="dropdown-menu">
            <li>
                <button class="dropdown-item tombol-tambah-transaksi" data-bs-toggle="modal" data-bs-target="#transaksiModal">
                    Tambah Data
                </button>
            </li>
            <li>
                <hr class="dropdown-divider" />
            </li>
            <li>
                <button class="dropdown-item tombol-import_excel-transaksi" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                    Import Data
                </button>
            </li>
            <li>
                <hr class="dropdown-divider" />
            </li>
            <li>
                <button class="dropdown-item" onclick="downloadPDFTransaksi()">
                    Download PDF
                </button>
            </li>
            <li>
                <hr class="dropdown-divider" />
            </li>
            <li>
                <button class="dropdown-item" onclick="downloadExcel()">
                    Download Excel
                </button>
            </li>
        </ul>
    </div>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item">Manajemen Keuangan</li>
        <li class="breadcrumb-item active">Data Transaksi</li>
    </ol>
</nav>

@include('modal.transaksi.index')
@include('modal.transaksi.import_excel')
@include('modal.transaksi.upload')

<div class="card">
    <div class="card-header mb-3">
        <div class="filter mt-3 ml-auto">
            <ul class="nav nav-underline">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/transaksi">Transaksi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/compare">Compare Pengeluaran</a>
                </li>
            </ul>
            @if(session('success'))
            <div style="color: green;">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div style="color: red;">
                {{ session('error') }}
            </div>
            @endif

            <form>
                <div class="form-row align-items-center d-flex">
                    <div class="col mx-3">
                        <p class="filter">Tanggal Transaksi</p>
                        <div id="daterange" class="daterange"> <i class="fa fa-calendar"></i>&nbsp;
                            <span style="font-weight: bold;"></span>
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
                    <div class="col">
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
    </div>
    <div class="card-body">
        <table id="transaksiTable" class="customTable">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Tanggal Transaksi</th>
                    <th class="text-center">Pemasukan</th>
                    <th class="text-center">Nominal Pemasukan</th>
                    <th class="text-center">Pengeluaran</th>
                    <th class="text-center">Nominal Pengeluaran</th>
                    <th class="text-center" style="width: 350px;">Keterangan</th>
                    <th class="text-center">Dibuat Tanggal</th>
                    <th class="text-center">Diupdate Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
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
<script src="{{ asset('js/transaksi.js') }}"></script>
@endsection