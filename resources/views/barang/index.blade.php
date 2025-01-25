<head>
    <title>Barang</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')
<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Barang</h1>
    <div class="btn-group">
        <button class="btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Action
        </button>
        <ul class="dropdown-menu">
            <li>
                <button class="dropdown-item tombol-tambah-barang" data-bs-toggle="modal" data-bs-target="#barangModal">
                    Tambah Data
                </button>
            </li>
            <li>
                <button class="dropdown-item" onclick="downloadPDFbarang()" disabled>
                    Download PDF
                </button>
            </li>
            <li>
                <button class="dropdown-item" onclick="downloadExcel()" disabled>
                    Download Excel
                </button>
            </li>
        </ul>
    </div>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item">Manajemen Stok Barang</li>
        <li class="breadcrumb-item active">Barang</li>
    </ol>
</nav>

@include('modal.barang.index')

<div class="card">
    <div class="card-header mb-3">
        <div class="filter mt-3 ml-auto">
            <form>
                <div class="form-row align-items-center">
                    <div class="col-md-3">
                        <select class="form-control select-2" name="status">
                            <option value="">Tampilkan Semua</option>
                            <option value="terbeli">Terbeli</option>
                            <option value="belum terbeli">Belum Terbeli</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        <table id="barangTable" class="customTable">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Nama Toko</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Jumlah Barang</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Dibuat Tanggal</th>
                    <th class="text-center">Diupdate Tanggal</th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/barang.js') }}"></script>
@endsection