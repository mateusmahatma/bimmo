<!DOCTYPE html>
<html lang="id">

<head>
    <title>Tambah Dana Darurat</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Tambah Data Dana Darurat</a>
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
        <form action="{{ route('dana-darurat.store') }}" method="POST">
            @csrf

            <div class="mt-3 mb-3">
                <label for="tgl_transaksi_dana_darurat" class="form-label required">Tanggal Transaksi</label>
                <input name="tgl_transaksi_dana_darurat" type="date" class="form-control" placeholder="Tanggal Transaksi"
                    value="{{ old('tgl_transaksi_dana_darurat', $dana->tgl_transaksi_dana_darurat) }}">
            </div>

            <div class="mb-3">
                <label for="jenis_transaksi_dana_darurat" class="form-label">Jenis Transaksi</label>
                <select name="jenis_transaksi_dana_darurat" class="form-control">
                    <option value="">-- Pilih Status --</option>
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Masuk</option>
                    <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="nominal_dana_darurat" class="form-label">Nominal Dana Darurat</label>
                <input type="number" class="form-control" name="nominal_dana_darurat" placeholder="Nominal Dana Darurat"
                    value="{{ old('nominal_dana_darurat', $dana->nominal_dana_darurat) }}">
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" name="keterangan" placeholder="Keterangan"></textarea> </textarea>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/barang.js') }}?v={{ filemtime(public_path('js/barang.js')) }}"></script>
@endsection