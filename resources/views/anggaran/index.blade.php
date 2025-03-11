<head>
    <title>Anggaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')
<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Anggaran</h1>
    <div class="btn-group">
        <button class="btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Action
        </button>
        <ul class="dropdown-menu">
            <li>
                <button class="dropdown-item tombol-tambah-anggaran" data-bs-toggle="modal" data-bs-target="#anggaranModal">
                    Tambah Data
                </button>
            </li>
        </ul>
    </div>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item">Manajemen Keuangan</li>
        <li class="breadcrumb-item active">Anggaran</li>
    </ol>
</nav>

@include('modal.anggaran.index')

<div class="card">
    <div class="card-header">
    </div>
    <div class="card-body">
        <table id="anggaranTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 5px;">No</th>
                    <th class="text-center">Nama Anggaran</th>
                    <th class="text-center">Persentase</th>
                    <th style="width: 90px;">Dibuat Tanggal</th>
                    <th style="width: 90px;">Diupdate Tanggal</th>
                    <th style="width: 5px;">Aksi</th>
                </tr>
            </thead>
        </table>
        <div>
            <div class="badge-primary" style="font-size: medium;">Total Persentase: <span id="totalPersentase">0</span>%</div>
            <span id="exceedMessage" style="color: red; font-size: medium; margin-left: 10px;"></span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/anggaran.js') }}"></script>
@endsection