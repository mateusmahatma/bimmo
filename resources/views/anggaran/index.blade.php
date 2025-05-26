<!DOCTYPE html>
<html lang="id">

<head>
    <title>Budget List</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/anggaran">Budget List</a>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link tombol-tambah-anggaran" href="#" data-bs-toggle="modal" data-bs-target="#pemasukanModal">
                <span class="badge-primary rounded-pill">Add Data</span>
            </a>
        </li>
    </ul>
</nav>

@include('modal.anggaran.index')

<div class="card-header">
    <div class="card-body">
        <table id="anggaranTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 5px;">No</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Percentage</th>
                    <th class="text-center">Expense Type</th>
                    <th style="width: 90px;">Created Date</th>
                    <th style="width: 90px;">Updated Date</th>
                    <th style="width: 1px;"></th>
                </tr>
            </thead>
        </table>
        <div>
            <div class="badge-success" style="font-size: medium;">Total Percentage: <span id="totalPersentase">0</span>%</div>
            <span id="exceedMessage" style="color: red; font-size: medium; margin-left: 10px;"></span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/anggaran.js') }}?v={{ filemtime(public_path('js/anggaran.js')) }}"></script>
@endsection