<!DOCTYPE html>
<html lang="id">

<head>
    <title>Monitoring Anggaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar px-3">
    <a class="navbar-brand" href="/anggaran">Monitoring Anggaran</a>
</nav>

<div class="card-header">
    <div class="card-body">
        <div class="custom-alert">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">Langkah-langkah untuk memproses anggaran</h5>
                <button id="toggleBtn" class="btn btn-sm btn-outline-secondary">
                    <span id="toggleIcon">+</span>
                </button>
            </div>
            <ol id="detailContent" class="mt-3 ps-3">
                <li>Isi kolom <strong>Input Pendapatan Bulanan</strong>.</li>
                <li>Isi kolom <strong>Pendapatan Tambahan</strong> (opsional).</li>
                <li>Pilih rentang tanggal periode anggaran.</li>
                <li>Klik tombol <strong>Proses</strong>.</li>
            </ol>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Input Data Anggaran</h5>
                <button class="btn btn-light btn-sm">
                    <i class="bi bi-info-circle me-1"></i> Bantuan
                </button>
            </div>
            <div class="card-body">
                <form method="post" action="/kalkulator" id="formKalkulator" autocomplete="off">
                    @csrf
                    <div class="row g-4">
                        <!-- Bagian Pendapatan -->
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <h6 class="text-secondary mb-3">
                                    <i class="bi bi-cash-stack me-1"></i> Pendapatan
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">Pendapatan Tetap Bulanan (Wajib)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="monthly_income" placeholder="5.000.000" required>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Pendapatan Lain (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="additional_income" placeholder="1.000.000">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian Periode -->
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <h6 class="text-secondary mb-3">
                                    <i class="bi bi-calendar3 me-1"></i> Periode
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Mulai Anggaran</label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Tanggal Akhir Anggaran</label>
                                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="reset" class="btn btn-outline-secondary px-4 me-2">Bersihkan</button>
                        <button type="submit" class="btn btn-success px-4" id="btnProses">Hitung Anggaran</button>
                    </div>
                </form>
            </div>
        </div>


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