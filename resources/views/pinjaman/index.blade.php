<head>
    <title>Daftar Pinjaman</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Daftar Pinjaman</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pinjaman.create') }}">
                <span class="badge-primary rounded-pill">Tambah Data</span>
            </a>
        </li>
    </ul>
</nav>

@include('modal.pinjaman.index')

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="card-header">
    <div class="card-body">
        <table id="pinjamanTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 3px;">No</th>
                    <th class="text-center">Nama Pinjaman</th>
                    <th class="text-center">Nominal Pinjaman</th>
                    <th class="text-center">Status</th>
                    <th style="width: 3px;">Aksi</th>
                </tr>
            </thead>
        </table>
        <div class="badge-danger" style="font-size: medium">
            Total Pinjaman: <span id="totalPinjaman">0</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pinjaman.js') }}?v={{ filemtime(public_path('js/pinjaman.js')) }}"></script>
@endsection