@extends('layouts.main')
@section('container')
<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                Selamat Datang, {{ auth()->user()->name }}
            </li>
        </ol>
    </nav>
</div>

<section class="section dashboard" id="dashboard-container">
    <div class="row">
        <div class="card-nominal">
            <span class="badge-toggle toggle-button" onclick="toggleNominal()">Show/Hide Nominal</span>
            <ul class="box-info">
                <li>
                    <i class='bx bxs-dollar-circle'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominal) }}">{{ number_format($totalNominal) }}</h3>
                        <p>Total Pengeluaran Hari ini</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-coin-stack'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominalBulan) }}">{{ number_format($totalNominalBulan) }}</h3>
                        <p>Total Pengeluaran Bulan ini</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-coin-stack'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominalBulanPemasukan) }}">{{ number_format($totalNominalBulanPemasukan) }}</h3>
                        <p>Total Pemasukan Bulan ini</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-dollar-circle'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominalSisa) }}">{{ number_format($totalNominalSisa) }}</h3>
                        <p>Saldo Bulan ini</p>
                    </span>
                </li>
            </ul>
        </div>

        <!-- Cash Flow -->
        <div class="card-penyiar mb-4">
            <h3>Cash Flow</h3>
            <div class="card-goal">
                <div class="card-body-goal">
                    <div class="chart-container">
                        <div id="columnChart"></div> <!-- Ganti dengan div untuk ApexCharts -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Chart -->
        <div class="card-grafik" id="barChartCard">
            <h3>Expense Chart</h3>
            <div id="barChart"></div>
        </div>

        <!-- Transaksi Hari ini -->
        <div class="card-today">
            <h3>Recent Transaction</h3>
            <div class="card-body">
                <table id="todayTransactionsTable" class="display">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Pemasukan</th>
                            <th class="text-center">Nominal Pemasukan</th>
                            <th class="text-center">Pengeluaran</th>
                            <th class="text-center">Nominal Pengeluaran</th>
                            <th class="text-center" style="width: 100%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kompas -->
        <div class="card-today">
            <h3>Compass</h3>
            <div>
                <div id="chart"></div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection