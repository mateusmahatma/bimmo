<head>
    <title>Dana Darurat</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Dana Darurat</h1>
</div>

<div class="card">
    <div class="card-header mb-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Riwayat Transaksi Dana Darurat</h5>
        <ul class="nav nav-underline">
            <li class="nav-item">
                <a class="nav-link active" href="/transaksi">Transaksi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#transaksiModal">Tambah Data</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <table id="danaDaruratTable" class="customTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Transaksi</th>
                    <th>Jenis Transaksi</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Dibuat Tanggal</th>
                    <th>Diubah Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/dana-darurat.js') }}"></script>
@endsection