<head>
    <title>Detail Pinjaman</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')
@include('modal.pinjaman.bayar')

<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <h1>Detail Pinjaman</h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item link"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item">Manajemen Keuangan</li>
        <li class="breadcrumb-item link"><a href="/pinjaman">Daftar Pinjaman</a></li>
        <li class="breadcrumb-item">Detail Pinjaman</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header mb-3">
    </div>
    <div class="card-body">
        <div class="row">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
            <h5 class="card-title">Informasi Pinjaman</h5>
            <div class="col-md-6 mb-3">
                <strong>Nama Pinjaman:</strong>
                <p class="mb-0">{{ $pinjaman->nama_pinjaman }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Jumlah Pinjaman:</strong>
                <p class="mb-0">Rp {{ number_format($pinjaman->jumlah_pinjaman, 2, ',', '.') }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Jangka Waktu:</strong>
                <p class="mb-0">{{ $pinjaman->jangka_waktu }} bulan</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Tanggal Mulai:</strong>
                <p class="mb-0">{{ $pinjaman->start_date }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Tanggal Berakhir:</strong>
                <p class="mb-0">{{ $pinjaman->end_date }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Status:</strong>
                <p class="mb-0 text-capitalize">
                    @if ($pinjaman->status === 'belum_lunas')
                    <span class="badge badge-danger">Belum Lunas</span>
                    @elseif ($pinjaman->status === 'lunas')
                    <span class="badge badge-success">Lunas</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="card-header mb-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Riwayat Pembayaran Pinjaman</h5>
        <button type="button" class="btn-sm btn-color" data-bs-toggle="modal" data-bs-target="#bayarModal" data-pinjaman-id="{{ $pinjaman->id }}">
            Bayar Pinjaman
        </button>
    </div>
    <div class="card-body">
        <table class="customTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jumlah Angsuran</th>
                    <th>Tanggal Pembayaran</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($pinjaman->bayar_pinjaman as $bayar_pinjaman)
                <tr>
                    <td>{{ $no++ }}</td>
                    <!-- <td>{{ $bayar_pinjaman->id }}</td> -->
                    <td>Rp {{ number_format($bayar_pinjaman->jumlah_bayar, 2, ',', '.') }}</td>
                    <td>{{ $bayar_pinjaman->tgl_bayar }}</td>
                    <td>
                        <form action="{{ route('bayar_pinjaman.destroy', $bayar_pinjaman->id_bayar) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm btn-color2" onclick="return confirm('Anda yakin ingin menghapus?')"><i class="fas fa-trash"></i></button>
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
<script src="{{ asset('js/pinjaman.js') }}"></script>
@endsection