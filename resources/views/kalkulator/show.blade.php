<!DOCTYPE html>
<html lang="id">

<head>
    <title>Detail Anggaran</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Detail Anggaran</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
        </li>
    </ul>
</nav>

<div class="card-header">
    <div class="card-body">
        <div class="custom-alert" role="alert">
            <div style="display: flex; gap: 30px; align-items: flex-start;">
                <table class="customTable2">
                    <tbody>
                        <tr>
                            <th>Nama Anggaran</th>
                            <th>:</th>
                            <td>{{ $HasilProsesAnggaran->nama_anggaran }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai Anggaran</th>
                            <th>:</th>
                            <td>{{ \Carbon\Carbon::parse($HasilProsesAnggaran->tanggal_mulai)->locale('id')->isoFormat('D MMMM Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Akhir Anggaran</th>
                            <th>:</th>
                            <td>{{ \Carbon\Carbon::parse($HasilProsesAnggaran->tanggal_selesai)->locale('id')->isoFormat('D MMMM Y') }}</td>
                        </tr>
                        <tr>
                            <th>Persentase Anggaran</th>
                            <th>:</th>
                            <td>{{ $HasilProsesAnggaran->persentase_anggaran }}%</td>
                        </tr>
                        <tr>
                            <th>Nominal Anggaran</th>
                            <th>:</th>
                            <td>Rp {{ number_format($HasilProsesAnggaran->nominal_anggaran, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Sisa Anggaran</th>
                            <th>:</th>
                            <td>
                                Rp {{ number_format($HasilProsesAnggaran->sisa_anggaran, 2, ',', '.') }} <br>

                                @php
                                $sisa = $HasilProsesAnggaran->sisa_anggaran;
                                @endphp

                                @if ($sisa < 0)
                                    <span class="d-inline-flex align-items-center px-1 py-0.5 rounded small"
                                    style="background-color:#f8d7da; color:#721c24; font-size:10px;">
                                    <i class="bi bi-x-circle me-1" style="font-size:10px;"></i>
                                    Melebihi Anggaran
                                    </span>
                                    @else
                                    <span class="d-inline-flex align-items-center px-1 py-0.5 rounded small"
                                        style="background-color:#d4edda; color:#155724; font-size:10px;">
                                        <i class="bi bi-check-circle me-1" style="font-size:10px;"></i>
                                        Sesuai Anggaran
                                    </span>
                                    @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Tabel Jenis Pengeluaran di kanan -->
                <table class="customTable2">
                    <tbody>
                        <tr>
                            <th class="align-top">Jenis Pengeluaran</th>
                            <th class="align-top">:</th>
                            <td>
                                <ol id="list-pengeluaran" class="mb-0">
                                    @foreach ($namaPengeluaran as $index => $nama)
                                    <li class="{{ $index >= 3 ? 'hidden-item' : '' }}">{{ $nama }}</li>
                                    @endforeach
                                </ol>

                                @if ($total > 3)
                                <button id="toggleButton" type="button" class="btn btn-sm btn-primary mt-2">
                                    Show more
                                </button>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <input type="hidden" id="kalkulator-id" value="{{ $HasilProsesAnggaran->id_proses_anggaran }}">

        <table id="detailAnggaran" class="customTable">
            <thead>
                <tr>
                    <th style="width: 1%;">No</th>
                    <th>Tanggal Transaksi</th>
                    <th>Jenis Pengeluaran</th>
                    <th>Nominal</th>
                    <th style="width:35%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>


    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endsection