<head>
    <title>Daftar Pinjaman</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Daftar Pinjaman</h1>
    <div class="btn-group">
        <a href="{{ route('pinjaman.create') }}">
            <button class="btn-sm btn-color">
                <i class="fa fa-plus"></i> Tambah Pinjaman
            </button>
        </a>
    </div>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item">Manajemen Keuangan</li>
        <li class="breadcrumb-item active">Data Pinjaman</li>
    </ol>
</nav>

@include('modal.pinjaman.index')

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-header">
    </div>
    <div class="card-body">
        <table id="pinjamanTable" class="customTable">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama Pinjaman</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="badge-danger" style="font-size: medium">
            Total Pinjaman: <span id="totalPinjaman">0</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pinjaman.js') }}"></script>
@endsection