@extends('layouts.main')

@section('title', 'Income Categories')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1>Income Categories</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Income Categories</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-dashboard">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <h5 class="card-title mb-0 fw-bold">List of Income Categories</h5>
                                <p class="text-muted small mb-0">Manage your income sources here.</p>
                            </div>
                            <button id="btnBulkDelete" class="btn btn-danger btn-sm d-none">
                                <i class="bi bi-trash me-1"></i> Delete Selected (<span id="countSelected">0</span>)
                            </button>
                        </div>
                        <button type="button" class="btn btn-success tombol-tambah-pemasukan">
                            <i class="bi bi-plus-lg me-1"></i> Add New Category
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="pemasukanTable" class="table table-hover table-borderless align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;" class="text-center">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </th>
                                    <th style="width: 5%;">No</th>
                                    <th>Name</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-center">Last Updated</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables will populate this --}}
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@include('modal.pemasukan.index') 

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pemasukan.js') }}?v={{ filemtime(public_path('js/pemasukan.js')) }}"></script>
@endpush