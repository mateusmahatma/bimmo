@extends('layouts.main')

@section('title', 'Kalkulator Budget')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('container')
<div class="pagetitle mb-4">
    <h1>Budget Monitoring</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Budget Monitoring</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Input Form -->
        <div class="col-lg-12 mb-4">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Calculate New Budget</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Enter income and period to calculate the Budget.</p>
                    </div>
                    <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsCollapse" aria-expanded="false" aria-controls="instructionsCollapse">
                        <i class="bi bi-info-circle me-1"></i> Instructions
                    </button>
                </div>

                <div class="collapse" id="instructionsCollapse">
                    <div class="card-body bg-light border-bottom">
                        <div class="alert alert-info border-0 bg-white shadow-sm mb-0 text-dark">
                            <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb me-2"></i>Steps:</h6>
                            <ol class="mb-0 ps-3 small">
                                <li>Fill in the <strong>Monthly Fixed Income</strong> field.</li>
                                <li>Fill in the <strong>Additional Income</strong> field (if any).</li>
                                <li>Select the <strong>Date Range</strong> for the Budget period.</li>
                                <li>Click the <strong>Process Budget</strong> button.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
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
                                <label class="form-label fw-bold small text-uppercase text-muted">Periode Budget</label>
                                <div class="mb-3">
                                    <label class="form-label">Rentang Tanggal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                                        <div id="daterange" class="form-control" style="cursor: pointer;">
                                            <span></span>
                                        </div>
                                        <input type="hidden" name="tanggal_mulai" id="tanggal_mulai">
                                        <input type="hidden" name="tanggal_selesai" id="tanggal_selesai">
                                    </div>
                                    <div class="form-text small">Pilih tanggal mulai dan tanggal akhir untuk perhitungan Budget.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-light rounded-pill px-3 shadow-sm" id="btnReset">Reset</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" id="btnProses">
                                <i class="bi bi-Monitoring me-1"></i> Proses Budget
                                <span id="btnProsesSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Result Table -->
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                         <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Riwayat Proses Budget</h5>
                         <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Daftar riwayat perhitungan Budget Anda.</p>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm d-none rounded-pill px-3" id="btnBulkDelete">
                        <i class="bi bi-trash me-1"></i> Delete (<span id="countSelected">0</span>)
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="hasilAnggaranTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-light">
                                <tr style="border-bottom: 2px solid #edf2f9;">
                                    <th style="width: 5%;" class="text-center">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3 text-center">No</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">Periode</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">Nama Anggaran</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">Jenis Pengeluaran</th>
                                    <th class="text-center text-secondary small text-uppercase fw-bold py-3">Persentase</th>
                                    <th class="text-end text-secondary small text-uppercase fw-bold py-3">Nominal Anggaran</th>
                                    <th class="text-end text-secondary small text-uppercase fw-bold py-3">Terpakai</th>
                                    <th class="text-end text-secondary small text-uppercase fw-bold py-3">Sisa</th>
                                    <th style="width: 5%;" class="text-center text-secondary small text-uppercase fw-bold py-3">Aksi</th>
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
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endpush