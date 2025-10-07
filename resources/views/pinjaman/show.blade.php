<!DOCTYPE html>
<html lang="id">

<head>
    <title>Detail Pinjaman</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')
@include('modal.pinjaman.bayar')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Detail Pinjaman</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
        </li>
    </ul>
</nav>

<div class="card-header">
    <div class="card-body">
        <div class="custom-alert" role="alert">
            <!-- Detail Pinjaman -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    Nama Pinjaman:
                    <p class="mb-0">{{ $pinjaman->nama_pinjaman }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    Jumlah Pinjaman:
                    <p class="mb-0">Rp {{ number_format($pinjaman->jumlah_pinjaman, 2, ',', '.') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    Jangka Waktu:
                    <p class="mb-0">{{ $pinjaman->jangka_waktu }} bulan</p>
                </div>
                <div class="col-md-6 mb-3">
                    Tanggal Mulai:
                    <p class="mb-0">{{ $pinjaman->start_date }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    Tanggal Berakhir:
                    <p class="mb-0">{{ $pinjaman->end_date }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    Status:
                    <p class="mb-0 text-capitalize">
                        @if ($pinjaman->status === 'belum_lunas')
                        <span class="d-inline-flex align-items-center px-2 py-1 rounded small fw-semibold" style="background-color:#f8d7da; color:#721c24;">Belum Lunas</span>
                        @elseif ($pinjaman->status === 'lunas')
                        <span class="d-inline-flex align-items-center px-2 py-1 rounded small fw-semibold" style="background-color:#d4edda; color:#155724;">Lunas</span>
                        @endif
                    </p>
                </div>
            </div>

            <hr>
            <p class="mb-0">Harap tinjau kembali nilai-nilai di atas untuk memastikan keakuratan data Anda.</p>
        </div>

        <div class="card-header mb-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Riwayat Pembayaran Pinjaman</h5>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bayarModal" data-pinjaman-id="{{ $pinjaman->id }}">
                Bayar Pinjaman
            </button>
        </div>

        <table class="customTable">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th>Jumlah Angsuran</th>
                    <th>Tanggal Pembayaran</th>
                    <th style="width: 8%;"></th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($pinjaman->bayar_pinjaman as $bayar_pinjaman)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>Rp {{ number_format($bayar_pinjaman->jumlah_bayar, 2, ',', '.') }}</td>
                    <td>{{ $bayar_pinjaman->tgl_bayar }}</td>
                    <td>
                        <form action="{{ route('bayar_pinjaman.destroy', $bayar_pinjaman->id_bayar) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pinjaman.js') }}?v={{ filemtime(public_path('js/pinjaman.js')) }}"></script>
@endsection