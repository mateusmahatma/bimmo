<!DOCTYPE html>
<html lang="id">

<head>
    <title>Emergency Fund</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/dana-darurat">Emergency Fund</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link tombol-tambah-anggaran" href="#" data-bs-toggle="modal" data-bs-target="#danaDaruratModal">
                <span class="badge-primary rounded-pill">Add Data</span>
            </a>
        </li>
    </ul>
</nav>

@include('modal.dana_darurat.index')

<div class="card-header">
    <div class="card-body">
        <table id="danaDaruratTable" class="customTable">
            <thead>
                <tr>
                    <th scope="col" style="width: 1px;">No</th>
                    <th>Transaction Date</th>
                    <th>Transaction Type</th>
                    <th>Nominal</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th scope="col" style="width: 1px;"></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="badge-success" style="font-size: small;">
            Total Dana Darurat: <span id="totalDanaDarurat">0</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/dana-darurat.js') }}?v={{ filemtime(public_path('js/dana-darurat.js')) }}"></script>
@endsection