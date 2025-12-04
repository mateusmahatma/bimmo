<!DOCTYPE html>
<html lang="id">

<head>
    <title>Pengeluaran</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Edit Kategori Pengeluaran</a>
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
        <form action="{{ route('pengeluaran.update', $pengeluaran->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mt-3 mb-3">
                <label for="nama" class="form-label required">Nama Kategori Pengeluaran</label>
                <input name="nama" class="form-control" placeholder="Nama Kategori Pengeluaran"
                    value="{{ old('nama', $pengeluaran->nama) }}">
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pengeluaran.js') }}?v={{ filemtime(public_path('js/pengeluaran.js')) }}"></script>
@endsection