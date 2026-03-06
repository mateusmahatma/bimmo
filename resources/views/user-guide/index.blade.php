@extends('layouts.main')

@section('container')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <!-- Hero Section -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                <div class="card-body p-5 text-white position-relative">
                    <div class="position-relative z-index-1">
                        <h1 class="display-5 fw-bold mb-3">Selamat Datang di Bimmo! 🚀</h1>
                        <p class="lead mb-0 opacity-75">Panduan langkah demi langkah untuk mengelola keuangan Anda dengan lebih pintar dan teratur.</p>
                    </div>
                    <div class="position-absolute end-0 bottom-0 mb-n5 me-n5 opacity-25">
                        <i class="bi bi-journal-bookmark-fill" style="font-size: 15rem;"></i>
                    </div>
                </div>
            </div>

            <!-- Steps Section -->
            <div class="row g-4">
                <!-- Step 1: Data Entry -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="bi bi-1-circle-fill text-primary fs-3"></i>
                                </div>
                                <h3 class="fw-bold mb-0">Langkah Pertama: Masukkan Data Keuangan</h3>
                            </div>
                            
                            <p class="text-muted mb-4">Hal terpenting dalam Bimmo adalah mencatat arus kas Anda. Mulailah dengan memasukkan data pemasukan dan pengeluaran harian Anda.</p>
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="p-3 border rounded-3 bg-light hover-shadow transition-all h-100">
                                        <h5 class="fw-bold d-flex align-items-center">
                                            <i class="bi bi-plus-circle-fill text-success me-2"></i>
                                            <a href="{{ route('pemasukan.index') }}" target="_blank" class="text-decoration-none text-dark">Pemasukan</a>
                                        </h5>
                                        <p class="small text-muted mb-0">Catat semua sumber pendapatan Anda, seperti gaji, bonus, atau investasi.</p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 border rounded-3 bg-light hover-shadow transition-all h-100">
                                        <h5 class="fw-bold d-flex align-items-center">
                                            <i class="bi bi-dash-circle-fill text-danger me-2"></i>
                                            <a href="{{ route('pengeluaran.index') }}" target="_blank" class="text-decoration-none text-dark">Pengeluaran</a>
                                        </h5>
                                        <p class="small text-muted mb-0">Input belanja harian, tagihan, dan pengeluaran rutin lainnya untuk melacak kemana uang Anda pergi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Wallet Management -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 p-2 rounded-circle me-3">
                                    <i class="bi bi-2-circle-fill text-info fs-4"></i>
                                </div>
                                <h4 class="fw-bold mb-0">Kelola <a href="{{ route('dompet.index') }}" target="_blank" class="text-decoration-none text-info">Dompet</a></h4>
                            </div>
                            <p class="text-muted small">Pisahkan dana Anda ke dalam berbagai kategori dompet (misalnya: Tunai, Tabungan, E-Wallet) untuk pengelolaan yang lebih presisi.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Emergency Fund -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
                                    <i class="bi bi-3-circle-fill text-warning fs-4"></i>
                                </div>
                                <h4 class="fw-bold mb-0">Siapkan <a href="{{ route('dana-darurat.index') }}" target="_blank" class="text-decoration-none text-warning">Dana Darurat</a></h4>
                            </div>
                            <p class="text-muted small">Lindungi diri Anda dengan menyiapkan dana cadangan. Bimmo membantu Anda menghitung berapa banyak yang Anda butuhkan dan melacak kemajuan tabungan Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Budget Planning -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-dark bg-opacity-10 p-2 rounded-circle me-3">
                                    <i class="bi bi-4-circle-fill text-dark fs-4"></i>
                                </div>
                                <h4 class="fw-bold mb-0">Perencanaan & Monitoring Anggaran</h4>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start border-start border-4 border-primary ps-3">
                                        <div>
                                            <h6 class="fw-bold mb-1"><a href="{{ route('anggaran.index') }}" target="_blank" class="text-decoration-none text-primary">Budget Categories</a></h6>
                                            <p class="small text-muted mb-0">Buat rencana anggaran untuk setiap kategori pengeluaran Anda agar tidak terjadi "over-budget".</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start border-start border-4 border-success ps-3">
                                        <div>
                                            <h6 class="fw-bold mb-1"><a href="{{ route('kalkulator.index') }}" target="_blank" class="text-decoration-none text-success">Budget Monitoring</a></h6>
                                            <p class="small text-muted mb-0">Lihat kalkulasi mendalam dan analisis apakah keuangan Anda sudah berjalan sesuai rencana.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Advanced Features -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm mb-5" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-star-fill text-warning me-2"></i>
                                Fitur Lanjutan Lainnya
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a href="{{ route('tujuan-keuangan.index') }}" target="_blank" class="d-block p-3 border rounded-3 text-center text-decoration-none bg-light hover-primary transition-all">
                                        <i class="bi bi-bullseye fs-3 mb-2 d-block"></i>
                                        <span class="fw-bold text-dark">Financial Goals</span>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('aset.index') }}" target="_blank" class="d-block p-3 border rounded-3 text-center text-decoration-none bg-light hover-primary transition-all">
                                        <i class="bi bi-box-seam fs-3 mb-2 d-block"></i>
                                        <span class="fw-bold text-dark">Inventory Assets</span>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('pinjaman.index') }}" target="_blank" class="d-block p-3 border rounded-3 text-center text-decoration-none bg-light hover-primary transition-all">
                                        <i class="bi bi-arrow-down-up fs-3 mb-2 d-block"></i>
                                        <span class="fw-bold text-dark">Liability</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Quote -->
            <div class="text-center pb-5">
                <blockquote class="blockquote">
                    <p class="mb-2 italic text-muted">"Keuangan yang terkontrol adalah langkah pertama menuju kebebasan finansial."</p>
                    <footer class="blockquote-footer small">Tim Bimmo</footer>
                </blockquote>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-3px);
    }
    .hover-primary:hover {
        background-color: #f0f7ff !important;
        border-color: #3b82f6 !important;
        transform: translateY(-3px);
    }
    .hover-primary:hover i, .hover-primary:hover span {
        color: #3b82f6 !important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .z-index-1 {
        z-index: 1;
    }
    #main-content {
        background-color: #f8fafc;
    }
</style>
@endsection
