<head>
    <title>Kalkulator Anggaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<div class="pagetitle">
    <h1>Kalkulator Anggaran</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item">Manajemen Keuangan</li>
            <li class="breadcrumb-item active">Kalkulator Anggaran</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">

        <form method="post" action="/kalkulator/calculate">
            @csrf
            <div class="mt-3 mb-3">
                <div class="alert alert-info" role="alert">
                    <strong>Informasi :</strong>
                    <br>Persentase alokasi anggaran berdasarkan pada menu "Anggaran"</br>
                </div>
                <label for="monthly_income" class="form-label">Penghasilan Bulanan:</label>
                <input type="number" name="monthly_income" class="form-control" oninput="formatCurrency(this)" required>
            </div>
            <div class="mb-3">
                <label for="additional_income" class="form-label">Pendapatan Tambahan:</label>
                <input type="number" name="additional_income" class="form-control" oninput="formatCurrency(this)">
            </div>

            <button type="submit" class="btn-sm btn-color">Cek Hasil</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/kalkulator.js') }}"></script>
@endsection