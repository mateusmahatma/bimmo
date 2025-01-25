<head>
    <title>Jenis Pengeluaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Jenis Pengeluaran</h1>
    <div class="btn-group">
        <button class="btn-sm btn-color tombol-tambah-pemasukan" data-bs-toggle="modal" data-bs-target="#pengeluaranModal">
            <i class="fa fa-plus"></i> Tambah Data </button>
    </div>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item">Manajemen Keuangan</li>
        <li class="breadcrumb-item active">Jenis Pengeluaran</li>
    </ol>
</nav>

@include('modal.pengeluaran.index')

<div class="card">
    <div class="card-header">
    </div>
    <div class="card-body">
        <table id="pengeluaranTable" class="customTable">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama</th>
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
<script src="{{ asset('js/pengeluaran.js') }}"></script>
@endsection