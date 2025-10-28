<!DOCTYPE html>
<html lang="id">

<head>
    <title>Tambah Anggaran</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Tambah Data Anggaran</a>
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
        <form action="{{ route('anggaran.store') }}" method="POST">
            @csrf

            <div class="mt-3 mb-3">
                <label for="nama_anggaran" class="form-label">Nama Anggaran</label>
                <input name="nama_anggaran" id="nama_anggaran" class="form-control" placeholder="Nama Anggaran"
                    value="{{ old('nama_anggaran', $anggaran->nama_anggaran) }}" required>
            </div>

            <div class="mb-3">
                <label for="persentase_anggaran" class="form-label">Persentase Anggaran (%)</label>
                <input type="number" name="persentase_anggaran" id="persentase_anggaran"
                    class="form-control" placeholder="Persentase Anggaran"
                    value="{{ old('persentase_anggaran', $anggaran->persentase_anggaran) }}" required>
            </div>

            <div class="mb-3">
                <label for="id_pengeluaran" class="col-form-label">Jenis Pengeluaran</label>
                <select name="id_pengeluaran[]" id="id_pengeluaran" class="form-select" multiple>
                    @foreach ($pengeluarans as $pengeluaran)
                    <option value="{{ $pengeluaran->id }}"
                        {{ in_array($pengeluaran->id, (array) old('id_pengeluaran', $anggaran->id_pengeluaran)) ? 'selected' : '' }}>
                        {{ $pengeluaran->nama }}
                    </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/anggaran.js') }}?v={{ filemtime(public_path('js/anggaran.js')) }}"></script>
@endsection