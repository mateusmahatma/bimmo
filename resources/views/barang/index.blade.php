<head>
    <title>Asset List</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/barang">Asset List</a>
    <ul class="nav nav-pills">
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <span class="badge-primary dropdown-toggle">Action</span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#barangModal">Add Data</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadPDFbarang()">Download PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadExcel()">Download Excel</a></li>
            </ul>
        </li>
    </ul>
</nav>

@include('modal.barang.index')

<div class="card-header">
    <div class="card-body">
        <form>
            <div class="col-md-3">
                <select class="form-control select-2" name="status">
                    <option value="">Show Asset Status All</option>
                    <option value="1">Assets owned</option>
                    <option value="0">Mortgaged Assets</option>
                </select>
            </div>
        </form>
    </div>
    <div class="card-body">
        <table id="barangTable" class="customTable">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Store</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Total Assets</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Created Date</th>
                    <th class="text-center">Updated Date</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
        </table>
        <div class="badge-success" style="font-size: medium">
            Total assets owned: <span id="totalAset">Rp 0</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/barang.js') }}?v={{ filemtime(public_path('js/barang.js')) }}"></script>
@endsection