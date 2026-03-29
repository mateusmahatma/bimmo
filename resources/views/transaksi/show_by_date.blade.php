@extends('layouts.main')

@section('title', __('Transactions') . ' — ' . $dateFormatted)

@push('css')
<style>
    /* Header */
    .pagetitle { border-bottom: 1px solid #e9ecef; padding-bottom: 0.75rem; }
    .pagetitle h1 { font-size: 1.5rem; letter-spacing: -0.03em; color: #2d3436; }
    [data-bs-theme="dark"] .pagetitle { border-bottom-color: #2d2d2d; }
    [data-bs-theme="dark"] .pagetitle h1 { color: #e0e0e0; }

    .breadcrumb { font-size: 0.85rem; }
    .breadcrumb-item a { color: #636e72; text-decoration: none; transition: color 0.2s; }
    .breadcrumb-item a:hover { color: #0984e3; }
    .breadcrumb-item.active { color: #0984e3; font-weight: 600; }
    .breadcrumb-item + .breadcrumb-item::before {
        content: "\F285"; font-family: "bootstrap-icons";
        font-size: 0.65rem; color: #b2bec3;
        padding-right: 0.5rem; padding-left: 0.5rem;
    }

    /* Cards */
    .card-dashboard {
        border-radius: 20px; border: 1px solid rgba(0,0,0,0.05);
        background: #fff; color: #2d3436;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    [data-bs-theme="dark"] .card-dashboard {
        background-color: #1e1e1e; border-color: rgba(255,255,255,0.05);
    }

    /* Transaction rows */
    .tx-row {
        border-radius: 12px; padding: 0.5rem 0.85rem;
        border: 1px solid rgba(0,0,0,0.05);
        background: #fff; transition: box-shadow 0.2s, border-color 0.2s;
    }
    .tx-row:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-color: rgba(13,110,253,0.2); }
    [data-bs-theme="dark"] .tx-row { background: #1a1a1a; border-color: rgba(255,255,255,0.05); }
    [data-bs-theme="dark"] .tx-row:hover { border-color: rgba(96,165,250,0.3); }

    /* FAB */
    .fab-add {
        position: fixed; bottom: 2rem; right: 1.5rem; z-index: 1040;
        width: 60px; height: 60px; border-radius: 50%;
        display: none; align-items: center; justify-content: center;
        box-shadow: 0 8px 16px rgba(13,110,253,0.4);
    }
    @media (max-width: 767.98px) {
        .fab-add { display: flex; }
        .btn-add-desktop { display: none; }
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="fw-bold mb-1">
                <i class="bi bi-calendar3 me-2 text-primary"></i>{{ $dateFormatted }}
            </h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">{{ __('Transactions') }}</a></li>
                    <li class="breadcrumb-item active">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('transaksi.create', ['date' => $date]) }}"
           class="btn btn-primary rounded-pill px-4 shadow-sm btn-add-desktop">
            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Transaction') }}
        </a>
    </div>
</div>

<section class="section">
    <div class="row">

        {{-- Summary Cards --}}
        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width:48px;height:48px;background:rgba(25,135,84,0.1);">
                            <i class="bi bi-arrow-down-circle fs-3 text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">{{ __('Total Income') }}</h6>
                            <h4 class="mb-0 fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width:48px;height:48px;background:rgba(220,53,69,0.1);">
                            <i class="bi bi-arrow-up-circle fs-3 text-danger"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">{{ __('Total Expense') }}</h6>
                            <h4 class="mb-0 fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width:48px;height:48px;background:rgba(13,110,253,0.1);">
                            <i class="bi bi-wallet2 fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">{{ __('Net Balance') }}</h6>
                            <h4 class="mb-0 fw-bold {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($netIncome, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction List --}}
        <div class="col-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size:1.1rem;">
                            {{ __('Transactions') }}
                        </h5>
                        <p class="text-muted small mb-0 mt-1">{{ $transaksi->count() }} {{ __('records on this date') }}</p>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($transaksi->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                            <p class="text-muted mt-3 mb-0">{{ __('No transactions on this date yet.') }}</p>
                            <a href="{{ route('transaksi.create', ['date' => $date]) }}"
                               class="btn btn-primary rounded-pill px-4 mt-3">
                                <i class="bi bi-plus-lg me-1"></i> {{ __('Add Transaction') }}
                            </a>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-2">
                            @foreach($transaksi as $t)
                                @php
                                    $isIncome  = (float)$t->nominal_pemasukan > 0;
                                    $isExpense = (float)$t->nominal > 0;
                                    $categoryName = $isIncome
                                        ? ($t->pemasukanRelation->nama ?? __('Income'))
                                        : ($t->pengeluaranRelation->nama ?? __('Expense'));
                                    $amount = $isIncome ? (float)$t->nominal_pemasukan : (float)$t->nominal;
                                    $amountClass = $isIncome ? 'text-success' : 'text-danger';
                                    $amountSign  = $isIncome ? '+' : '-';
                                    $iconClass   = $isIncome ? 'bi-arrow-down-circle-fill text-success' : 'bi-arrow-up-circle-fill text-danger';
                                @endphp
                                <div class="tx-row d-flex align-items-center gap-3">
                                    {{-- Icon --}}
                                    <div class="flex-shrink-0">
                                        <i class="bi {{ $iconClass }} fs-4"></i>
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold text-dark" style="font-size:0.95rem;">{{ $categoryName }}</div>
                                        @if($t->keterangan)
                                            <div class="text-muted small text-truncate" style="max-width:320px;">
                                                {!! strip_tags($t->keterangan) !!}
                                            </div>
                                        @endif
                                        @if($t->dompet)
                                            <span class="badge bg-light text-secondary border fw-normal rounded-pill px-2 py-0 mt-1" style="font-size:0.7rem;">
                                                <i class="bi bi-wallet2 me-1"></i>{{ $t->dompet->nama }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Amount + Actions --}}
                                    <div class="flex-shrink-0 text-end">
                                        <div class="fw-bold {{ $amountClass }}" style="font-size:0.95rem;">
                                            {{ $amountSign }} Rp {{ number_format($amount, 0, ',', '.') }}
                                        </div>
                                        <div class="d-flex gap-2 justify-content-end mt-1">
                                            <a href="{{ route('transaksi.edit', $t->hash) }}"
                                               class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-0"
                                               style="font-size:0.75rem;">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('transaksi.destroy', $t->hash) }}" method="POST"
                                                  onsubmit="return confirm('{{ __('Delete this transaction?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-outline-danger btn-sm rounded-pill px-2 py-0"
                                                        style="font-size:0.75rem;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</section>

{{-- FAB Mobile --}}
<a href="{{ route('transaksi.create', ['date' => $date]) }}" class="btn btn-primary fab-add" title="{{ __('Add Transaction') }}">
    <i class="bi bi-plus-lg fs-2"></i>
</a>

@endsection
