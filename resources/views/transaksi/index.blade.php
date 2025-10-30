<!DOCTYPE html>
<html lang="id">

<head>
    <title>Arus Kas</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/transaksi">Arus Kas</a>
    <ul class="nav nav-pills">
        <li class="nav-item dropdown">
            <div class="btn-group dropstart">
                <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Pilih Opsi
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#transaksiModal">Tambah Data</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importExcelModal">Impor Data</a></li>
                    <li><a class="dropdown-item" href="#" onclick="downloadPDFTransaksi()">Unduh PDF</a></li>
                    <li><a class="dropdown-item" href="#" onclick="downloadExcel()">Unduh Excel</a></li>
                    <li>
                        <hr class=" dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="/compare">Bandingkan Biaya Pengeluaran</a></li>
                </ul>
            </div>
        </li>
    </ul>
</nav>

@include('modal.transaksi.index')
@include('modal.transaksi.import_excel')
@include('modal.transaksi.upload')

<div class="card-header">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <p class="filter">Tanggal Transaksi</p>
                <div id="daterange" class="daterange">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span>
                    <i class="fa fa-caret-down"></i>
                </div>
            </div>
            <div class="col-md-4">
                <p class="filter d-flex align-items-center">
                    Jenis Pendapatan
                    <i class="bi bi-exclamation-circle ms-2 text-muted"
                        style="font-size: 14px; cursor: pointer;"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Filter dapat dilakukan lebih dari satu jenis pendapatan">
                    </i>
                </p>
                <select class="form-select" id="filter_pemasukan" name="filter_pemasukan" multiple>
                    <option value="">- Pilih -</option>
                    @foreach ($pemasukan as $pemasukan)
                    <option value="{{ $pemasukan->id }}">{{ $pemasukan->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <p class="filter d-flex align-items-center">
                    Jenis Pengeluaran
                    <i class="bi bi-exclamation-circle ms-2 text-muted"
                        style="font-size: 14px; cursor: pointer;"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Filter dapat dilakukan lebih dari satu jenis pengeluaran"></i>
                </p>
                <select class="form-select" id="filter_pengeluaran" name="filter_pengeluaran" multiple>
                    <option value="">- Pilih -</option>
                    @foreach ($pengeluaran as $pengeluaran)
                    <option value="{{ $pengeluaran->id }}">{{ $pengeluaran->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="custom-alert" role="alert">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="custom-alert-heading mb-0">Laporan Keuangan</h4>
                <button id="toggleBtn" class="btn btn-sm btn-outline-secondary" type="button">
                    <span id="toggleIcon">+</span>
                </button>
            </div>

            <div id="laporanContent" class="mt-3">
                <p class="mb-2">Di bawah ini adalah ringkasan total pendapatan dan pengeluaran berdasarkan data yang tersedia.</p>
                <div class="mt-3 mb-3">
                    <table class="table table-noborder mb-0" style="width:auto">
                        <tr>
                            <td><strong>Total Pendapatan</strong></td>
                            <td class="px-2">:</td>
                            <td><span id="totalPemasukan">0</span></td>
                        </tr>
                        <tr>
                            <td><strong>Total Pengeluaran</strong></td>
                            <td class="px-2">:</td>
                            <td><span id="totalPengeluaran">0</span></td>
                        </tr>
                        <tr>
                            <td><strong>Laba Bersih</strong></td>
                            <td class="px-2">:</td>
                            <td><span id="netIncome">0</span></td>
                        </tr>
                    </table>
                </div>
                <hr>
                <p class="mb-0">Harap tinjau data di bawah ini untuk memastikan keakuratan data Anda.</p>
            </div>
        </div>

        <table id="transaksiTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 1%;">No</th>
                    <th>Tanggal</th>
                    <th>Jenis Pemasukan</th>
                    <th>Nominal Pemasukan</th>
                    <th>Jenis Pengeluaran</th>
                    <th>Nominal Pengeluaran</th>
                    <th style="width:20%">Keterangan</th>
                    <th>Dibuat</th>
                    <th>Diperbarui</th>
                    <th style="width: 1%;"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src=" {{ asset('js/transaksi.js') }}?v={{ filemtime(public_path('js/transaksi.js')) }}">
</script>
@endsection