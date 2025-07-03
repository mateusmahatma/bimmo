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

<nav id="navbar-example2" class="navbar px-3">
    <a class="navbar-brand" href="/pemasukan">Jenis Pemasukan</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link tombol-tambah-pemasukan" href="#" data-bs-toggle="modal" data-bs-target="#pemasukanModal">
                <span class="badge-primary">Tambah Data</span>
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
                    <th scope="col" style="width: 1px;">No</th>
                    <th scope="col" class="text-center align-middle" style="width: 250px;">Nama</th>
                    <th scope="col" class="text-center align-middle">Created</th>
                    <th scope="col" class="text-center align-middle">Updated</th>
                    <th scope="col" style="width: 1px;"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/pemasukan.js') }}?v={{ filemtime(public_path('js/pemasukan.js')) }}"></script>
@endsection