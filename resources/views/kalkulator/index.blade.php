<head>
    <title>Kalkulator Anggaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/anggaran">Kalkulator Anggaran</a>
</nav>

<div class="card-header">
    <div class="card-body">
        <form method="post" action="/kalkulator/calculate">
            @csrf
            <div class="mt-3 mb-3">
                <div class="alert alert-info d-flex align-items-start gap-2" role="alert">
                    <i class="bi bi-info-circle-fill fs-4"></i>
                    <div>
                        <strong>Informasi:</strong>
                        <p class="mb-0">
                            Persentase alokasi anggaran berdasarkan pada "Daftar Anggaran" yang sudah dibuat
                        </p>
                    </div>
                </div>
                <label for="monthly_income" class="form-label">Penghasilan Bulanan:</label>
                <input type="number" name="monthly_income" class="form-control" oninput="formatCurrency(this)" required>
            </div>
            <div class="mb-3">
                <label for="additional_income" class="form-label">Pendapatan Tambahan:</label>
                <input type="number" name="additional_income" class="form-control" oninput="formatCurrency(this)">
            </div>

            <button type="submit" class="cssbuttons-io-button">Hitung</button>
            <!-- <button type="submit" class="btn-sm btn-color">Hitung</button> -->
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/kalkulator.js') }}"></script>
@endsection