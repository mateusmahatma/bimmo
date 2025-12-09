<!DOCTYPE html>
<html lang="id">

<head>
    <title>Transaksi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="#">Tambah Data Transaksi</a>
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
        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf

            <div class="mt-3 mb-3">
                <label for="tgl_transaksi" class="form-label required">Tanggal Transaksi</label>
                <input name="tgl_transaksi" class="form-control" type="date"
                    value="{{ old('tgl_transaksi', $transaksi->tgl_transaksi) }}">
            </div>

            <div class="mb-3">
                <label for="pemasukan" class="col-form-label">Pemasukan</label>
                <select class="form-select" id="pemasukan" name="pemasukan">
                    <option value="">- Pilih -</option>
                    @foreach ($pemasukan as $item)
                    <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text">Rp</span>
                <input type="number" id="nominal_pemasukan" name="nominal_pemasukan" class="form-control" placeholder="Nominal Pemasukan">
                <span class="input-group-text">.00</span>
            </div>
            <div class="mb-3">
                <label for="pengeluaran" class="col-form-label">Pengeluaran</label>
                <select class="form-select" id="pengeluaran" name="pengeluaran">
                    <option value="">- Pilih -</option>
                    @foreach ($pengeluaran as $item)
                    <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text">Rp</span>
                <input type="number" id="nominal" name="nominal" class="form-control" placeholder="Nominal Pengeluaran">
                <span class="input-group-text">.00</span>
            </div>

            <div class="custom-alert">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">Masukkan nominal ke Aset/Dana Darurat</h5>
                    <button id="toggleBtn" type="button" class="btn btn-sm btn-outline-secondary">
                        <span id="toggleIcon">+</span>
                    </button>
                </div>
                <ol id="detailContent" class="mt-3 ps-3">
                    <!-- Checkbox kategori -->
                    <div class="alert alert-success">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="asset_list" id="checkAssetList" name="kategori[]">
                            <label class="form-check-label" for="checkAssetList">
                                Masukkan ke Daftar Aset
                                <small class="text-muted d-block">
                                    Jika Anda mencentang opsi ini, transaksi akan ditambahkan ke daftar aset.
                                </small>
                            </label>
                        </div>

                        <div class="mb-3" id="selectBarangContainer" style="display: none;">
                            <label for="barang_id" class="form-label">Pilih Aset</label>
                            <select class="form-select" id="barang_id" name="barang_id">
                                <option value="">- Pilih -</option>
                                @foreach ($barang as $barang)
                                <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- {{-- Checkbox Dana Darurat --}} -->
                    <div class="alert alert-danger">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="emergency_fund" id="checkEmergencyFund" name="kategori[]">
                            <label class="form-check-label" for="checkEmergencyFund">
                                Masukkan ke Dana Darurat
                                <small class="text-muted d-block">
                                    Jika Anda mencentang opsi ini, transaksi akan ditambahkan ke dana darurat.
                                </small>
                            </label>
                        </div>

                        <!-- {{-- Form dana darurat --}} -->
                        <div id="danaDaruratContainer" style="display: none;">

                            <div class="mb-3">
                                <label class="form-label">Jenis Transaksi Dana Darurat</label>
                                <select name="jenis_transaksi_dana_darurat" class="form-select">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="1">Dana Masuk</option>
                                    <option value="2">Dana Keluar</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nominal Dana Darurat</label>
                                <input type="number" name="nominal_dana_darurat" class="form-control" placeholder="Nominal">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan Dana Darurat</label>
                                <textarea name="keterangan_dana_darurat" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </ol>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="col-form-label">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="form-control" placeholder="Keterangan"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('checkAssetList').addEventListener('change', function() {
        const container = document.getElementById('selectBarangContainer');
        container.style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('checkEmergencyFund').addEventListener('change', function() {
        const container = document.getElementById('danaDaruratContainer');
        container.style.display = this.checked ? 'block' : 'none';
    });

    // --- Toggle untuk bagian pertama ---
    const toggleBtn = document.getElementById("toggleBtn");
    const toggleIcon = document.getElementById("toggleIcon");
    const detailContent = document.getElementById("detailContent");

    toggleBtn.addEventListener("click", () => {
        const isHidden = detailContent.style.display === "none";
        detailContent.style.display = isHidden ? "block" : "none";
        toggleIcon.textContent = isHidden ? "−" : "+";
    });

    // default: sembunyikan konten
    detailContent.style.display = "none";
</script>
@endsection


@section('scripts')
<script src="{{ asset('js/transaksi.js') }}?v={{ filemtime(public_path('js/transaksi.js')) }}"></script>
@endsection