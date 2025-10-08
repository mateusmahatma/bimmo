<!DOCTYPE html>
<html lang="id">

<head>
    <title>Daftar Pinjaman</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav class="navbar px-3">
    <a class="navbar-brand" href="#">Daftar Pinjaman</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a href="{{ route('pinjaman.create') }}" class="btn btn-success">
                Tambah Data
            </a>
        </li>
    </ul>
</nav>

@include('modal.pinjaman.index')

<div class="card-header">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <p class="filter d-flex align-items-center">
                    Status Pinjaman
                    <i class="bi bi-exclamation-circle ms-2 text-muted"
                        style="font-size: 14px; cursor: pointer;"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Pilih status pinjaman belum lunas atau lunas">
                    </i>
                </p>
                <select class="form-select" id="filter_status" name="filter_status">
                    <option value="">Belum Lunas dan Lunas</option>
                    <option value="belum_lunas">Belum Lunas</option>
                    <option value="lunas">Lunas</option>
                </select>
            </div>
        </div>
        <div class="custom-alert" role="alert">
            <h4 class="custom-alert-heading">Laporan Pinjaman</h4>
            <p class="mb-2">Di bawah ini adalah ringkasan total pinjaman berdasarkan data yang tersedia.</p>
            <div class="mt-3 mb-3">
                <table class="table table-noborder mb-0" style="width:auto">
                    <tr>
                        <td><strong>Total Pinjaman</strong></td>
                        <td class="px-2">:</td>
                        <td><span id="totalPinjaman">0</span></td>
                    </tr>
                </table>
            </div>

            <hr>
            <p class="mb-0">Harap tinjau kembali nilai-nilai di atas untuk memastikan keakuratan data Anda.</p>
        </div>
        <table id="pinjamanTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 1%;">No</th>
                    <th>Nama Pinjaman</th>
                    <th>Nominal Pinjaman</th>
                    <th>Status</th>
                    <th style="width: 1%;"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pinjaman.js') }}?v={{ filemtime(public_path('js/pinjaman.js')) }}"></script>
@endsection