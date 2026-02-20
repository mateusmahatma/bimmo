@extends('layouts.main')

@section('title', 'Dana Darurat')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1>Dana Darurat</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Dana Darurat</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            
             <!-- Total Card -->
             <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(45deg, #4158d0, #c850c0);">
                <div class="card-body p-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1" style="opacity: 0.9;">Total Emergency Fund</h6>
                        <h2 class="mb-0 fw-bold" id="totalDanaDarurat">Rp {{ number_format($totalDanaDarurat, 0, ',', '.') }}</h2>
                        <div class="mt-2">
                            <small class="opacity-75">Target: Rp {{ number_format($targetDanaDarurat, 0, ',', '.') }}</small>
                            <div class="progress mt-1" style="height: 6px; background-color: rgba(255,255,255,0.2);">
                                <div class="progress-bar bg-white" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="opacity-75">{{ $percentage }}% Terlaksana</small>
                        </div>
                    </div>
                    <div class="fs-1 opacity-25">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>

            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Daftar Transaksi Dana Darurat</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Kelola riwayat penambahan atau pengurangan dana darurat.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAturTarget">
                            <i class="bi bi-gear me-1"></i> Atur Target
                        </button>
                        <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> Delete Selected (<span id="countSelected">0</span>)
                        </button>
                        <a href="{{ route('dana-darurat.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Data
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="danaDaruratTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-light">
                                <tr style="border-bottom: 2px solid #edf2f9;">
                                    <th style="width: 5%;" class="text-center py-3">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="checkAll" style="cursor: pointer;">
                                        </div>
                                    </th>
                                    <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3 text-center">No</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Tanggal</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Jenis</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Nominal</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Catatan</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Dibuat</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Diperbarui</th>
                                    <th style="width: 10%;" class="text-center text-secondary small text-uppercase fw-bold py-3">Aksi</th>
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

@include('modal.dana_darurat.index')
@include('dana_darurat.target_modal')

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/dana-darurat.js') }}?v={{ filemtime(public_path('js/dana-darurat.js')) }}"></script>
@endpush