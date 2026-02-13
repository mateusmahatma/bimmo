@extends('layouts.main')

@section('title', 'Kalkulator Anggaran')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('container')
<div class="pagetitle mb-4">
    <h1>Kalkulator Anggaran</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Kalkulator Anggaran</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Input Form -->
        <div class="col-lg-12">
            <div class="card-dashboard mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Hitung Anggaran Baru</h5>
                            <p class="text-muted small mb-0">Masukkan pendapatan dan periode untuk menghitung anggaran.</p>
                        </div>
                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsCollapse" aria-expanded="false" aria-controls="instructionsCollapse">
                            <i class="bi bi-info-circle me-1"></i> Petunjuk
                        </button>
                    </div>

                    <div class="collapse mb-4" id="instructionsCollapse">
                        <div class="alert alert-info border-0 bg-light text-dark">
                            <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb me-2"></i>Langkah-langkah:</h6>
                            <ol class="mb-0 ps-3 small">
                                <li>Isi kolom <strong>Pendapatan Tetap Bulanan</strong>.</li>
                                <li>Isi kolom <strong>Pendapatan Lain</strong> (jika ada).</li>
                                <li>Pilih <strong>Rentang Tanggal</strong> periode anggaran.</li>
                                <li>Klik tombol <strong>Proses Anggaran</strong>.</li>
                            </ol>
                        </div>
                    </div>

                    <form method="post" action="{{ route('kalkulator.store') }}" id="formKalkulator" autocomplete="off">
                        @csrf
                        <div class="row g-4">
                            <!-- Income Section -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Informasi Pendapatan</label>
                                <div class="mb-3">
                                    <label for="monthly_income" class="form-label">Pendapatan Tetap (Bulanan)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="text" class="form-control" id="monthly_income" name="monthly_income" placeholder="Contoh: 5.000.000" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="additional_income" class="form-label">Pendapatan Lain (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="text" class="form-control" id="additional_income" name="additional_income" placeholder="Contoh: 1.000.000">
                                    </div>
                                </div>
                            </div>

                            <!-- Period Section -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Periode Anggaran</label>
                                <div class="mb-3">
                                    <label class="form-label">Rentang Tanggal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                                        <div id="daterange" class="form-control" style="background: #fff; cursor: pointer;">
                                            <span></span>
                                        </div>
                                        <input type="hidden" name="tanggal_mulai" id="tanggal_mulai">
                                        <input type="hidden" name="tanggal_selesai" id="tanggal_selesai">
                                    </div>
                                    <div class="form-text small">Pilih tanggal mulai dan tanggal akhir untuk perhitungan anggaran.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-light" id="btnReset">Reset</button>
                            <button type="submit" class="btn btn-primary px-4" id="btnProses">
                                <i class="bi bi-calculator me-1"></i> Proses Anggaran
                                <span id="btnProsesSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Result Table -->
        <div class="col-lg-12">
            <div class="card-dashboard">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Riwayat Proses Anggaran</h5>
                    <div class="table-responsive">
                        <table id="hasilAnggaranTable" class="table table-hover table-borderless align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Periode</th>
                                    <th>Nama Anggaran</th>
                                    <th>Jenis Pengeluaran</th>
                                    <th class="text-center">Persentase</th>
                                    <th class="text-end">Nominal Anggaran</th>
                                    <th class="text-end">Terpakai</th>
                                    <th class="text-end">Sisa</th> <!-- Sisa Anggaran -->
                                    <th style="width: 5%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endpush