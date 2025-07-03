@extends('layouts.main')
@section('container')
<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                Hi, {{ auth()->user()->name }}
            </li>
        </ol>
    </nav>
</div>

<section class="section dashboard" id="dashboard-container">
    <div class="row">
        <div class="card-nominal">
            <h3>Financial Overview <img src="/icon/eye.png" style="height: 22px; width: 22px" onclick=" toggleNominal()"></img></h3>
            <ul class="box-info">
                <li>
                    <i class='bx bxs-chevrons-down'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominalBulanPemasukan) }}">{{ number_format($totalNominalBulanPemasukan) }}</h3>
                        <p>Total Income for the Month</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-chevrons-up'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominalBulan) }}">{{ number_format($totalNominalBulan) }}</h3>
                        <p>Total Expenses for the Month</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-chevron-up'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominal) }}">{{ number_format($totalNominal) }}</h3>
                        <p>Total Expenses Today</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-credit-card-alt'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominalSisa) }}">{{ number_format($totalNominalSisa) }}</h3>
                        <p>Balance of the Month</p>
                    </span>
                </li>
            </ul>
        </div>

        <!-- Cash Flow -->
        <div class="card-penyiar mb-4">
            <h3>Cash Flow</h3>
            <div class="filters-container">
                <div class="filter-wrapper">
                    <label for="filterPeriod" class="filter-label">Period:</label>
                    <select id="filterPeriod" class="filter-dropdown">
                        <option value="2">Last 2 Months</option>
                        <option value="4">Last 4 Months</option>
                        <option value="6" selected>Last 6 Months</option>
                        <option value="12">Last 1 Year</option>
                        <option value="all">Show All</option>
                    </select>
                </div>

                <div class="filter-wrapper">
                    <label for="chartType" class="filter-label">Show:</label>
                    <select id="chartType" class="filter-dropdown">
                        <option value="cashFlow">Bar</option>
                        <option value="incomeExpenses">Chart</option>
                    </select>
                </div>
            </div>

            <!-- Chart Container -->
            <div>
                <div id="columnChart" class="chart-container"></div>
                <div id="barChart" class="chart-container"></div>
            </div>
        </div>

        <!-- Today's Transaction -->
        <div class="card-today">
            <h3>Today's Transaction</h3>
            <div class="card-header">
                <table id="todayTransactionsTable" class="dashboardTable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Income Type</th>
                            <th class="text-center">Income Nominal</th>
                            <th class="text-center">Expenses Type</th>
                            <th class="text-center">Expenses Nominal</th>
                            <th class="text-center" style="width: 25%;">Description</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- Expense Bar -->
        <div class="card-today">
            <h3>Expenses Bar</h3>
            <div class="filters-container">
                <div class="filter-wrapper">
                    <label class="filter-label" for="filterMonth">Select Month:</label>
                    <select class="filter-dropdown" id="filterMonth">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                            @endfor
                    </select>
                </div>

                <div class="filter-wrapper">
                    <label class="filter-label" for="filterYear">Select Year:</label>
                    <select class="filter-dropdown" id="filterYear">
                        @for ($y = now()->year - 5; $y <= now()->year; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                    </select>
                </div>
            </div>
            <div id="barJenisPengeluaran"></div>
        </div>

        <!-- Kompas -->
        <div class="card-today">
            <h3>Performance Ratio</h3>
            <div id="chartKompas"
                data-rasio="{{ $rasio ?? 0 }}"
                data-rasio-inflasi="{{ $rasio_inflasi ?? 0 }}"
                data-rasio-dana-darurat="{{ $rasio_dana_darurat ?? 0 }}"
                data-rasio-pengeluaran-pendapatan="{{ $rasio_pengeluaran_pendapatan ?? 0 }}">
            </div>
        </div>

        <!-- Modal Kompas -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="display">
                            <tr>
                                <th>Formula</th>
                                <td><span id="modalRumus"></span></td>
                            </tr>
                            <tr>
                                <th>Target</th>
                                <td><span id="modalTarget"></span></td>
                            </tr>
                            <tr>
                                <th>Actual</th>
                                <td><span id="modalNominal"></span></td>
                            </tr>
                            <tr>
                                <th>Analysis</th>
                                <td><span id="modalAnalisis"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail Transaksi -->
        <div class="modal fade" id="detailModalBarPengeluaran" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Transaction Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="display">
                            <thead>
                                <tr>
                                    <th>Transaction Date</th>
                                    <th>Description</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody id="modalBodyBarPengeluaran">
                                <!-- Data transaksi akan diisi di sini melalui JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}"></script>
@endsection