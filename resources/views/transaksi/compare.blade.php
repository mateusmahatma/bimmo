<!DOCTYPE html>
<html lang="id">

<head>
    <title>Bandingkan Biaya Pengeluaran</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar px-3">
    <div class="d-flex align-items-center">
        <a class="navbar-brand me-2" href="/transaksi">Arus Kas</a>
        <span class="me-2">/</span>
        <a class="navbar-brand" href="/compare">Bandingkan Biaya Pengeluaran</a>
    </div>
</nav>

<div class="card-header">
    <div class="card-body">
        <form id="compareForm">
            <div class="row mb-3">
                <div class="col md-4">
                    <p class="filter">Pilih Tanggal Pengeluaran Periode 1</p>
                    <div id="daterange" class="daterange">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span style="font-weight: bold;"></span>
                        <i class="fa fa-caret-down"></i>
                    </div>
                    <input type="hidden" id="start_date_1" name="start_date_1">
                    <input type="hidden" id="end_date_1" name="end_date_1">
                </div>
                <div class="col mx-3">
                    <p class="filter">Pilih Tanggal Pengeluaran Periode 2</p>
                    <div id="daterange2" class="daterange">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span style="font-weight: bold;"></span>
                        <i class="fa fa-caret-down"></i>
                    </div>
                    <input type="hidden" id="start_date_2" name="start_date_2">
                    <input type="hidden" id="end_date_2" name="end_date_2">
                </div>
                <div class="col mx-3">
                    <p class="filter">Pilih Jenis Pengeluaran</p>
                    <select class="form-control" name="filter_pengeluaran" id="pengeluaran">
                        <option value="">- Pilih -</option>
                        @foreach ($pengeluaran as $pengeluaran)
                        <option value="{{ $pengeluaran->id }}">{{ $pengeluaran->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-success tombol-compare">Bandingkan</button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="custom-alert" role="alert">
            <div>
                <h4 class="custom-alert-heading">Information:</h4>
                <p class="mb-2"> GAP = Nominal Pengeluaran Periode 1 - Nominal Pengeluaran Periode 2.</p>
            </div>
        </div>
        <table id="comparisonTable" class="customTable">
            <thead>
                <tr>
                    <th class="text-center">Nominal Pengeluaran Periode 1</th>
                    <th class="text-center">Nominal Pengeluaran Periode 2</th>
                    <th class="text-center">GAP</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/compare.js') }}?v={{ filemtime(public_path('js/compare.js')) }}"></script>
@endsection