<!DOCTYPE html>
<html lang="id">

<head>
    <title>Tambah Pinjaman</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Tambah Data Pinjaman</a>
</nav>

<div class="card-header">
    <div class="card-body">
        <form action="{{ route('pinjaman.store') }}" method="POST">
            @csrf
            <div class="mt-3 mb-3">
                <label for="nama_pinjaman" class="form-label">Nama Pinjaman</label>
                <input name="nama_pinjaman" id="nama_pinjaman" class="form-control" step="0.01" placeholder="Contoh : Pinjaman Koperasi" required>
            </div>
            <div class="mb-3">
                <label for="jumlah_pinjaman" class="form-label">Nominal Pinjaman</label>
                <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" class="form-control" step="0.01" placeholder="2000000" required>
            </div>
            <div class="mb-3">
                <label for="nominal_awal" class="form-label">Nominal Cicilan</label>
                <input type="number" name="nominal_awal" id="nominal_awal" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="jumlah_angsuran" class="form-label">Jumlah Angsuran</label>
                <input type="number" name="jumlah_angsuran" id="jumlah_angsuran" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="angsuran_ke" class="form-label">Angsuran Ke</label>
                <input type="number" name="angsuran_ke" id="angsuran_ke" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nominal_sisa" class="form-label">Nominal Sisa Pinjaman</label>
                <input type="number" name="nominal_sisa" id="nominal_sisa" class="form-control" step="0.01" disabled required>
            </div>
            <div class="mb-3">
                <label for="sisa_angsuran" class="form-label">Sisa Angsuran</label>
                <input type="number" name="sisa_angsuran" id="sisa_angsuran" class="form-control" disabled required>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea type="text" name="keterangan" id="keterangan" class="form-control" required>
                </textarea>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
@endsection