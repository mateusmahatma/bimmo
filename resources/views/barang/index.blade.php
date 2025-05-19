<head>
    <title>Daftar Aset</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/barang">Daftar Aset</a>
    <ul class="nav nav-pills">
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <span class="badge-primary dropdown-toggle">Aksi</span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#barangModal">Tambah Data</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadPDFbarang()">Download PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadExcel()">Download Excel</a></li>
            </ul>
        </li>
    </ul>
</nav>

@include('modal.barang.index')

<div class="card-header">
    <div class="card-body">
        <form>
            <div class="col-md-3">
                <select class="form-control select-2" name="status">
                    <option value="">Tampilkan Status Aset Semua</option>
                    <option value="1">Aset Dimiliki</option>
                    <option value="0">Aset Digadaikan</option>
                </select>
            </div>
        </form>
    </div>
    <div class="card-body">
        <table id="barangTable" class="customTable">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama Aset</th>
                    <th class="text-center">Nama Toko</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Jumlah Aset</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Dibuat Tanggal</th>
                    <th class="text-center">Diupdate Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
        </table>
        <div class="badge-success" style="font-size: medium">
            Total Aset yang dimiliki: <span id="totalAset">Rp 0</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/barang.js') }}?v={{ filemtime(public_path('js/barang.js')) }}"></script>
@endsection