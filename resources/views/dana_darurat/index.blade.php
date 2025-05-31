<!DOCTYPE html>
<html lang="id">

<head>
    <title>Dana Darurat</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/dana-darurat">Dana Darurat</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link tombol-tambah-anggaran" href="#" data-bs-toggle="modal" data-bs-target="#">
                <span class="badge-primary rounded-pill">Tambah Data</span>
            </a>
        </li>
    </ul>
</nav>

<div class="card-header">
    <div class="card-body">
        <table id="danaDaruratTable" class="customTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Transaksi</th>
                    <th>Jenis Transaksi</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Dibuat Tanggal</th>
                    <th>Diubah Tanggal</th>
                    <th style="width: 1px;"></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="badge-success" style="font-size: medium;">
            Total Dana Darurat: <span id="totalDanaDarurat">0</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/dana-darurat.js') }}?v={{ filemtime(public_path('js/dana-darurat.js')) }}"></script>
@endsection