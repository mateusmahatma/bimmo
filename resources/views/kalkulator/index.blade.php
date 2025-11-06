<!DOCTYPE html>
<html lang="id">

<head>
    <title>Hasil Anggaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar px-3">
    <a class="navbar-brand" href="/anggaran">Hasil Anggaran</a>
</nav>

<div class="card-header">
    <div class="card-body">
        <div class="custom-alert" role="alert">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="custom-alert-heading mb-0">Langkah-langkah untuk memproses anggaran:</h4>
                <button id="toggleBtn" class="btn btn-sm btn-outline-secondary" type="button">
                    <span id="toggleIcon">+</span>
                </button>
            </div>
            <ol id="detailContent" class="mt-3">
                <li>Isi kolom "Input Pendapatan Bulanan".</li>
                <li>Isi kolom "Masukan Penghasilan Tambahan", kolom ini bersifat opsional.</li>
                <li>Pilih rentang tanggal untuk mengatur periode anggaran.</li>
                <li>Klik tombol "Proses"</li>
            </ol>
        </div>
        <form method="post" action="/kalkulator" id="formKalkulator" autocomplete="off">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <p for="monthly_income" class="filter required">Input Pendapatan Bulanan:</p>
                    <input type="number" name="monthly_income" class="form-control" placeholder="Enter here monthly income" required>
                </div>

                <div class="col-md-4">
                    <p for="additional_income" class="filter">Masukan Pendapatan Tambahan:</p>
                    <input type="number" name="additional_income" class="form-control" placeholder="Enter here additional income">
                </div>

                <div class="col-md-4">
                    <p for="daterange" class="filter">Pilih Tanggal Periode Anggaran:</p>
                    <div id="daterange" class="daterange" style="cursor: pointer;">
                        <i class="fa fa-calendar"></i>
                        <span></span>
                        <i class="fa fa-caret-down"></i>
                    </div>

                    <input type="hidden" name="tanggal_mulai" id="tanggal_mulai">
                    <input type="hidden" name="tanggal_selesai" id="tanggal_selesai">
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-warning" id="btnProses">
                        <span id="btnProsesSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="btnProsesText"><i class="fa fa-sync-alt"></i> Proses</span>
                    </button>
                </div>
            </div>
        </form>

        <table id="hasilAnggaranTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 1%;">No</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Akhir</th>
                    <th>Nama Anggaran</th>
                    <th style="width: 200px;">Jenis Pengeluaran</th>
                    <th>Persentase Anggaran</th>
                    <th>Jumlah Anggaran</th>
                    <th>Anggaran yang digunakan</th>
                    <th style="width: 8%">Sisa Anggaran</th>
                    <th style="width: 1%;"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endsection