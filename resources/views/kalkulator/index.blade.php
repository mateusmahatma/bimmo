<!DOCTYPE html>
<html lang="id">

<head>
    <title>Budget Results</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/anggaran">Budget Results</a>
</nav>

<div class="card-header">
    <div class="card-body">
        <form method="post" action="/kalkulator" id="formKalkulator" autocomplete="off">
            @csrf
            <div class="mt-3 mb-3">
                <label for="monthly_income" class="form-label required">Monthly Income:</label>
                <input type="number" name="monthly_income" class="form-control" placeholder="Enter here monthly income" required>
            </div>

            <div class="mb-3">
                <label for="additional_income" class="form-label">Additional Income:</label>
                <input type="number" name="additional_income" class="form-control" placeholder="Enter here additional income">
            </div>

            <div class="mb-3">
                <label for="daterange" class="form-label">Select Budget Period Date:</label>
                <div id="daterange" class="daterange" style="cursor: pointer;">
                    <i class="fa fa-calendar"></i>
                    <span>Select a date range</span>
                    <i class="fa fa-caret-down"></i>
                </div>

                <input type="hidden" name="tanggal_mulai" id="tanggal_mulai">
                <input type="hidden" name="tanggal_selesai" id="tanggal_selesai">
            </div>

            <div class="button-group">
                <button type="submit" class="cssbuttons-io-button" id="btnProses">
                    <span id="btnProsesSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span id="btnProsesText"><i class="fa fa-sync-alt"></i> Process</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card-header">
    <div class="card-body">
        <table id="hasilAnggaranTable" class="customTable">
            <thead>
                <tr>
                    <th style="width: 3px;">No</th>
                    <th class="text-center">Start Date</th>
                    <th class="text-center">End Date</th>
                    <th class="text-center">Budget Name</th>
                    <th class="text-center">Expense Type</th>
                    <th class="text-center">Budget Percentage</th>
                    <th class="text-center">Budget Amount</th>
                    <th class="text-center">Used Budget</th>
                    <th class="text-center">Remaining Budget</th>
                    <th style="width: 1px;"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endsection