@extends('layouts.main')

@section('container')
<div class="pagetitle mb-4">
    <h1>Dasbor</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                Hai, {{ auth()->user()->name }}
            </li>
        </ol>
    </nav>
</div>

{{-- Banner Dashboard --}}
{{-- Banner Slideshow --}}
<div id="dashboardCarousel" class="carousel slide mb-4 rounded-4 shadow-sm overflow-hidden" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="{{ asset('img/gambar1.png') }}" class="d-block w-100"
                alt="Banner 1" style="object-fit: cover; height: 250px;">
        </div>
        <div class="carousel-item">
            <img src="{{ asset('img/gambar2.png') }}" class="d-block w-100"
                alt="Banner 2" style="object-fit: cover; height: 250px;">
        </div>
        <!-- <div class="carousel-item">
            <img src="{{ asset('images/banner-dashboard-3.jpg') }}" class="d-block w-100"
                alt="Banner 3" style="object-fit: cover; height: 250px;">
        </div> -->
    </div>

    {{-- Tombol Navigasi --}}
    <button class="carousel-control-prev" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sebelumnya</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Selanjutnya</span>
    </button>
</div>



<div class="alert alert-danger">
    Peringatan Penting:
    <ul class="mb-0">
        <!-- @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach -->
        <li>Pinjaman Anda jatuh tempo pada tanggal 20 Juni 2000</li>
    </ul>
</div>

<section class="dashboard">
    <div class="row g-4">

        <!-- Kolom Kiri -->
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">

            {{-- Ikhtisar Keuangan --}}
            <div class="card-dashboard">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Ikhtisar Keuangan</h3>
                    <img src="/icon/eye.png" style="height:22px;width:22px;cursor:pointer" onclick="toggleNominal()">
                </div>
                <ul class="box-info">
                    <li>
                        <i class='bx bxs-chevrons-down icon-income'></i>
                        <div>
                            <h3 data-value="{{ number_format($totalNominalBulanPemasukan) }}">{{ number_format($totalNominalBulanPemasukan) }}</h3>
                            <p>Pendapatan Bulan Ini</p>
                        </div>
                    </li>
                    <li>
                        <i class='bx bxs-chevrons-up icon-expense'></i>
                        <div>
                            <h3 data-value="{{ number_format($totalNominalBulan) }}">{{ number_format($totalNominalBulan) }}</h3>
                            <p>Pengeluaran Bulan Ini</p>
                        </div>
                    </li>
                    <li>
                        <i class='bx bxs-chevron-up icon-today'></i>
                        <div>
                            <h3 data-value="{{ number_format($totalNominal) }}">{{ number_format($totalNominal) }}</h3>
                            <p>Pengeluaran Hari Ini</p>
                        </div>
                    </li>
                    <li>
                        <i class='bx bxs-credit-card-alt icon-balance'></i>
                        <div>
                            <h3 data-value="{{ number_format($totalNominalSisa) }}">{{ number_format($totalNominalSisa) }}</h3>
                            <p>Saldo Bulan Ini</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="card-dashboard">
                <div class="card-header d-flex justify-content-between align-items-center mb-3">
                    <h3>Arus Kas</h3>
                    <div class="d-flex gap-2">
                        <select id="filterPeriod" class="filter-dropdown">
                            <option value="2">2 Bulan</option>
                            <option value="4">4 Bulan</option>
                            <option value="6" selected>6 Bulan</option>
                            <option value="12">1 Tahun</option>
                            <option value="all">Semua</option>
                        </select>
                        <select id="chartType" class="filter-dropdown">
                            <option value="cashFlow">Bar</option>
                            <option value="incomeExpenses">Garis</option>
                        </select>
                    </div>
                </div>
                <div id="columnChart"></div>
                <div id="barChart"></div>
            </div>

            <div class="card-dashboard">
                <div class="card-header d-flex justify-content-between align-items-center mb-3">
                    <h3>Tingkat Tabungan</h3>
                    <select id="filterPeriodSavingRate" class="filter-dropdown">
                        <option value="2">2 Bulan</option>
                        <option value="4">4 Bulan</option>
                        <option value="6" selected>6 Bulan</option>
                        <option value="12">1 Tahun</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
                <div class="chart-container" id="savingRateChart"></div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">

            <div class="card-dashboard">
                <h3>Rasio Keuangan</h3>
                <div id="chartKompas"
                    data-rasio="{{ $rasio ?? 0 }}"
                    data-rasio-inflasi="{{ $rasio_inflasi ?? 0 }}"
                    data-rasio-dana-darurat="{{ $rasio_dana_darurat ?? 0 }}"
                    data-rasio-pengeluaran-pendapatan="{{ $rasio_pengeluaran_pendapatan ?? 0 }}">
                </div>
            </div>

            <div class="card-dashboard flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center mb-3">
                    <h3>Bilah Pengeluaran</h3>
                    <div class="d-flex gap-2">
                        <select id="filterMonth" class="filter-dropdown">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                                @endfor
                        </select>
                        <select id="filterYear" class="filter-dropdown">
                            @for ($y = now()->year - 5; $y <= now()->year; $y++)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                                @endfor
                        </select>
                    </div>
                </div>
                <div class="chart-container" id="barJenisPengeluaran"></div>
            </div>
        </div>

        <!-- Transaksi Hari Ini -->
        <div class="col-12">
            <div class="card-dashboard">
                <h3>Transaksi Hari Ini</h3>
                <table id="todayTransactionsTable" class="dashboardTable w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Pendapatan</th>
                            <th>Nominal Pendapatan</th>
                            <th>Jenis Pengeluaran</th>
                            <th>Nominal Pengeluaran</th>
                            <th style="width: 25%;">Keterangan</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('modal.dashboard.detail')
    @include('modal.dashboard.rasio')
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}"></script>
@endsection