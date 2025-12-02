<!DOCTYPE html>
<html lang="id">

<head>
    <title>Jenis Pemasukan</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/pemasukan">Jenis Pemasukan</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a href="{{ route('pemasukan.create') }}" class="btn btn-success">
                Tambah Data
            </a>
        </li>
    </ul>
</nav>

@include('modal.pemasukan.index')

<div class="card-header">
    <div class="card-body">
        <table id="pemasukanTable" class="customTable">
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
<script src="{{ asset('js/pemasukan.js') }}?v={{ filemtime(public_path('js/pemasukan.js')) }}"></script>
@endsection