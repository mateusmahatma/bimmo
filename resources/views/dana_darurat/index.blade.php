<!DOCTYPE html>
<html lang="id">

<head>
    <title>Dana Darurat</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/dana-darurat">Dana Darurat</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link tombol-tambah-anggaran" href="#" data-bs-toggle="modal" data-bs-target="#danaDaruratModal">
                <button class="btn btn-warning">Tambah Data</button>
            </a>
        </li>
    </ul>
</nav>

@include('modal.dana_darurat.index')

<div class="card-header">
    <div class="card-body">
        <table id="danaDaruratTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 1%;">No</th>
                    <th>Tanggal Transaksi</th>
                    <th>Jenis Transaksi</th>
                    <th>Nominal</th>
                    <th>Catatan</th>
                    <th>Dibuat</th>
                    <th>Diperbarui</th>
                    <th style="width: 1%;"></th>
                </tr>
            </thead>
        </table>
        <div class="badge-success" style="font-size: small;">
            Total Dana Darurat: <span id="totalDanaDarurat">0</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/dana-darurat.js') }}?v={{ filemtime(public_path('js/dana-darurat.js')) }}"></script>
@endsection