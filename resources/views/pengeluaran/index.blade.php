<!DOCTYPE html>
<html lang="id">

<head>
    <title>Kategori Pengeluaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/pengeluaran">Kategori Pengeluaran</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link tombol-tambah-pengeluaran" href="#" data-bs-toggle="modal" data-bs-target="#pengeluaranModal">
                <span class="btn btn-success">Tambah Data</span>
            </a>
        </li>
    </ul>
</nav>

@include('modal.pengeluaran.index')
@include('modal.pengeluaran.delete')

<div class="card-header">
    <div class="card-body">
        <table id="pengeluaranTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 1%;">No</th>
                    <th class="text-center" style="width: 600px;">Nama</th>
                    <th class="text-center">Dibuat Tanggal</th>
                    <th class="text-center">Diupdate Tanggal</th>
                    <th style="width: 1%;"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/pengeluaran.js') }}?v={{ filemtime(public_path('js/pengeluaran.js')) }}"></script>
@endsection