@extends('layouts.main')
@section('container')
<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                Halo, {{ auth()->user()->name }}
            </li>
        </ol>
    </nav>
</div>

<section class="section dashboard" id="dashboard-container">
    <div class="row">
        <!--  -->
        <div class="card-nominal">
            <div class="lock-icon">
                <i class="bx bx-lock" onclick="toggleNominal()"></i>
            </div>
            <ul class="box-info">
                <li>
                    <i class='bx bxs-chevrons-down'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominalBulanPemasukan) }}">{{ number_format($totalNominalBulanPemasukan) }}</h3>
                        <p>Total Pemasukan Bulan ini</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-chevrons-up'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominalBulan) }}">{{ number_format($totalNominalBulan) }}</h3>
                        <p>Total Pengeluaran Bulan ini</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-chevron-up'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominal) }}">{{ number_format($totalNominal) }}</h3>
                        <p>Total Pengeluaran Hari ini</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-credit-card-alt'></i>
                    <span class="text">
                        <h3 data-value="{{ number_format($totalNominalSisa) }}">{{ number_format($totalNominalSisa) }}</h3>
                        <p>Saldo Bulan ini</p>
                    </span>
                </li>
            </ul>
        </div>

        <!-- Arus Kas -->
        <div class="card-penyiar mb-4">
            <h3>Arus Kas</h3>
            <div class="filters-container">
                <div class="filter-wrapper">
                    <label for="filterPeriod" class="filter-label">Periode:</label>
                    <select id="filterPeriod" class="filter-dropdown">
                        <option value="2">2 Bulan Terakhir</option>
                        <option value="4">4 Bulan Terakhir</option>
                        <option value="6" selected>6 Bulan Terakhir</option>
                        <option value="12">1 Tahun Terakhir</option>
                        <option value="all">Tampilkan Semua</option>
                    </select>
                </div>

                <div class="filter-wrapper">
                    <label for="chartType" class="filter-label">Tampilkan:</label>
                    <select id="chartType" class="filter-dropdown">
                        <option value="cashFlow">Bar</option>
                        <option value="incomeExpense">Chart</option>
                    </select>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="chart-container">
                <div id="columnChart"></div>
                <div id="barChart"></div>
            </div>
        </div>

        <!-- Transaksi Hari ini -->
        <div class="card-today">
            <h3>Transaksi Hari Ini</h3>
            <div class="card-body">
                <table id="todayTransactionsTable" class="display">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Pemasukan</th>
                            <th class="text-center">Nominal Pemasukan</th>
                            <th class="text-center">Pengeluaran</th>
                            <th class="text-center">Nominal Pengeluaran</th>
                            <th class="text-center" style="width: 50%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bar Jenis Pengeluaran -->
        <div class="card-today">
            <h3>Bar Jenis Pengeluaran</h3>
            <div class="filters-container">
                <div class="filter-wrapper">
                    <label class="filter-label" for="filterMonth">Pilih Bulan:</label>
                    <select class="filter-dropdown" id="filterMonth">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                            @endfor
                    </select>
                </div>

                <div class="filter-wrapper">
                    <label class="filter-label" for="filterYear">Pilih Tahun:</label>
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
            <h3>Kompas</h3>
            <div id="chartKompas"
                data-rasio="{{ $rasio ?? 0 }}"
                data-rasio-inflasi="{{ $rasio_inflasi ?? 0 }}"
                data-rasio-dana-darurat=""
                data-rasio-pengeluaran-pendapatan="{{ $rasio_pengeluaran_pendapatan ?? 0 }}">
            </div>
        </div>

        <!-- Modal Detail -->
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
                                <th>Rumus</th>
                                <td><span id="modalRumus"></span></td>
                            </tr>
                            <tr>
                                <th>Target</th>
                                <td><span id="modalTarget"></span></td>
                            </tr>
                            <tr>
                                <th>Aktual</th>
                                <td><span id="modalNominal"></span></td>
                            </tr>
                            <tr>
                                <th>Analisis</th>
                                <td><span id="modalAnalisis"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal jenis pengeluaran -->
        <div class="modal fade" id="detailModalBarPengeluaran" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Detail Transaksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="display">
                            <thead>
                                <tr>
                                    <th>Keterangan</th>
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
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection