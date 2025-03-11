<head>
    <title>Jenis Pemasukan</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Jenis Pemasukan</h1>
    <div class="btn-group">
        <button class="cssbuttons-io-button tombol-tambah-pemasukan" data-bs-toggle="modal" data-bs-target="#pemasukanModal">
            <svg height="24" width="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 0h24v24H0z" fill="none"></path>
                <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z" fill="currentColor"></path>
            </svg>
            <span>Tambah Data</span>
        </button>
    </div>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item">Manajemen Keuangan</li>
        <li class="breadcrumb-item active">Jenis Pemasukan</li>
    </ol>
</nav>

@include('modal.pemasukan.index')

<div class="card">
    <div class="card-header">
    </div>
    <div class="card-body">
        <table id="pemasukanTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 5px;">No</th>
                    <th style="width: 400px;">Nama</th>
                    <th>Dibuat Tanggal</th>
                    <th>Diupdate Tanggal</th>
                    <th style=" width: 5px;">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/pemasukan.js') }}"></script>
@endsection