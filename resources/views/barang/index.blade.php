<!DOCTYPE html>
<html lang="id">

<head>
    <title>Daftar Aset</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar px-3">
    <a class="navbar-brand" href="/barang">Daftar Aset</a>
    <ul class="nav nav-pills">
        <div class="btn-group dropstart">
            <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Pilih Opsi
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#barangModal">Tambah Data</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadPDFbarang()">Unduh PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadExcel()">Unduh Excel</a></li>
            </ul>
        </div>
    </ul>
</nav>

@include('modal.barang.index')

<div class="card-header">
    <div class="card-body">
        <div class="custom-alert" role="alert">
            <div class="mt-3 mb-3">
                <table class="table table-noborder mb-0" style="width:auto">
                    <tr>
                        <td>Total aset yang tercatat</td>
                        <td class="px-2">:</td>
                        <td>
                            <span id="totalAset">0</span>
                            <span id="exceedMessage" style="color: red; font-size: small; margin-left: 10px;"></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <table id="barangTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 1%;">No</th>
                    <th class="text-center align-middle" style="width: 250px;">Nama</th>
                    <th class="text-center align-middle">Menyimpan</th>
                    <th class="text-center align-middle">Harga</th>
                    <th class="text-center align-middle">Status</th>
                    <th class="text-center align-middle">Dibuat</th>
                    <th class="text-center align-middle">Diperbarui</th>
                    <th style="width: 1%;"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/barang.js') }}?v={{ filemtime(public_path('js/barang.js')) }}"></script>
@endsection