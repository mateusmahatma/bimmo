<head>
    <title>Kalkulator Anggaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/anggaran">Proses Anggaran</a>
</nav>

<div class="card-header">
    <div class="card-body">
        <form method="post" action="/kalkulator" id="formKalkulator" autocomplete="off">
            @csrf
            <div class="mt-3 mb-3">
                <label for="monthly_income" class="form-label">Penghasilan Bulanan:</label>
                <input type="number" name="monthly_income" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="additional_income" class="form-label">Pendapatan Tambahan:</label>
                <input type="number" name="additional_income" class="form-control">
            </div>

            <div class="mb-3">
                <label for="daterange" class="form-label">Pilih Tanggal Periode Anggaran:</label>
                <div id="daterange" class="daterange" style="cursor: pointer;">
                    <i class="fa fa-calendar"></i>
                    <span>Pilih rentang tanggal</span>
                    <i class="fa fa-caret-down"></i>
                </div>

                <!-- Hidden inputs untuk dikirim ke backend -->
                <input type="hidden" name="tanggal_mulai" id="tanggal_mulai">
                <input type="hidden" name="tanggal_selesai" id="tanggal_selesai">
            </div>

            <div class="button-group">
                <button type="submit" class="cssbuttons-io-button" id="btnProses">
                    <span id="btnProsesSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span id="btnProsesText"><i class="fa fa-sync-alt"></i> Proses</span>
                </button>
            </div>
        </form>


    </div>
</div>

<div class="card-header">
    <div class="card-body">
        <div class="callout">
            <h4>Tabel Anggaran</h4>
        </div>
        <table id="hasilAnggaranTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 3px;">No</th>
                    <th class="text-center">Tanggal Mulai</th>
                    <th class="text-center">Tanggal Selesai</th>
                    <th class="text-center">Nama Anggaran</th>
                    <th class="text-center">Jenis Pengeluaran</th>
                    <th class="text-center">Persentase Anggaran</th>
                    <th class="text-center">Nominal Anggaran</th>
                    <th class="text-center">Anggaran Yang Digunakan</th>
                    <th class="text-center">Sisa Anggaran</th>
                    <th style="width: 3px;">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endsection