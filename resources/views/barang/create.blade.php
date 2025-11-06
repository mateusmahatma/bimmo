<!DOCTYPE html>
<html lang="id">

<head>
    <title>Tambah Aset</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Tambah Data Aset</a>
</nav>

<div class="card-header">
    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form action="{{ route('barang.store') }}" method="POST">
            @csrf

            <div class="mt-3 mb-3">
                <label for="nama_barang" class="form-label required">Nama Aset</label>
                <input name="nama_barang" id="nama_barang" class="form-control" placeholder="Nama Aset"
                    value="{{ old('nama_barang', $barang->nama_barang) }}">
            </div>

            <div class="mb-3">
                <label for="status" class="form-label required">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">-- Pilih Status --</option>
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Aset Dimiliki</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Aset Tidak Dimiliki</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="nama_toko" class="form-label">Store</label>
                <input type="text" id="nama_toko" class="form-control" name="nama_toko" placeholder="Store"
                    value="{{ old('nama_toko', $barang->nama_toko) }}">
            </div>

            <div class="mb-3">
                <label for="harga" class="form-label">Nominal Aset</label>
                <input type="number" class="form-control" id="harga" name="harga" step="0.01" placeholder="Nominal Aset"
                    value="{{ old('harga', $barang->harga) }}">
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/barang.js') }}?v={{ filemtime(public_path('js/barang.js')) }}"></script>
@endsection