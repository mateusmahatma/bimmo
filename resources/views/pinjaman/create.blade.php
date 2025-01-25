<head>
    <title>Tambah Pinjaman</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<div class="pagetitle">
    <h1>Tambah Pinjaman</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item">Manajemen Keuangan</li>
            <li class="breadcrumb-item link"><a href="/pinjaman">Daftar Pinjaman</a></li>
            <li class="breadcrumb-item active">Tambah Pinjaman</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('pinjaman.store') }}" method="POST">
            @csrf
            <div class="mt-3 mb-3">
                <label for="nama_pinjaman" class="form-label">Nama Pinjaman</label>
                <input name="nama_pinjaman" id="nama_pinjaman" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="jumlah_pinjaman" class="form-label">Jumlah Pinjaman</label>
                <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="jangka_waktu" class="form-label">Jangka Waktu (bulan)</label>
                <input type="number" name="jangka_waktu" id="jangka_waktu" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">Tanggal Berakhir</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="lunas">Lunas</option>
                    <option value="belum_lunas">Belum Lunas</option>
                </select>
            </div>
            <button type="submit" class="btn-sm btn-color">Simpan</button>
        </form>
    </div>
</div>
@endsection