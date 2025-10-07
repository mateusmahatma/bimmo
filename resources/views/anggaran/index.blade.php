<!DOCTYPE html>
<html lang="id">

<head>
    <title>Anggaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')
<div id="pjax-container">
    <nav class="navbar px-3">
        <a class="navbar-brand" href="/anggaran">Daftar Anggaran</a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <button class="btn btn-warning tombol-tambah-anggaran"
                    data-bs-toggle="modal" data-bs-target="#pemasukanModal">
                    Tambah Data
                </button>
            </li>
        </ul>
    </nav>

    @include('modal.anggaran.index')

    <div class="card-header">
        <div class="card-body">
            <div class="custom-alert" role="alert">
                <h4 class="custom-alert-heading">Laporan Persentase</h4>
                <p class="mb-0">
                    Di bawah ini adalah persentase total berdasarkan data yang dihitung.
                    Peringatan akan muncul jika nilainya melebihi batas yang diizinkan.
                </p>

                <div class="mt-3 mb-3">
                    <table class="table table-noborder mb-0" style="width:auto">
                        <tr>
                            <td><strong>Total Persentase</strong></td>
                            <td class="px-2">:</td>
                            <td>
                                <span id="totalPersentase"></span>
                                <span id="exceedMessage"
                                    style="color: red; font-size: small; margin-left: 10px;"></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <hr>
                <p class="mb-0">
                    Pastikan persentasenya tidak melebihi 100% agar tetap berada di dalam anggaran yang dialokasikan.
                </p>
            </div>

            <table id="anggaranTable" class="customTable">
                <thead>
                    <tr>
                        <th style="width: 1%;">No</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Persentase</th>
                        <th class="text-center col-expense-type">Jenis Pengeluaran</th>
                        <th class="text-center">Dibuat</th>
                        <th class="text-center">Diperbarui</th>
                        <th style="width: 1%;"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/anggaran.js') }}?v={{ filemtime(public_path('js/anggaran.js')) }}"></script>
@endsection