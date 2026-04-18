<div class="pagetitle mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1">{{ __('Net Worth Detail') }}</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Net Worth') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-0 px-4 shadow-sm border" wire:navigate>
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="container-fluid p-0">
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm overflow-hidden rounded-0">
                <div class="card-header border-0 pt-4 px-4 d-flex justify-content-between align-items-center net-worth-card-header">
                    <div>
                        <h5 class="fw-bold mb-0">{{ __('Net Worth Growth') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Historical trend of your assets and liabilities') }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm rounded-0 net-worth-sync-btn" type="button" id="btnSyncNetWorth" title="{{ __('Sync Data') }}">
                            <i class="bi bi-arrow-repeat me-1"></i> {{ __('Sync') }}
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="netWorthLoading" class="text-center p-5">
                        <div class="spinner-border text-primary net-worth-page-spinner" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted fw-medium">{{ __('Calculating your wealth history...') }}</p>
                    </div>
                    <div id="netWorthContent" class="d-none">
                        <div id="netWorthChart" class="net-worth-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm overflow-hidden rounded-0">
                <div class="card-header border-0 pt-4 px-4 net-worth-card-header">
                    <h5 class="fw-bold mb-0">{{ __('Monthly Data') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Click on values to see details') }}</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 net-worth-table">
                            <thead class="table-header-strip">
                                <tr>
                                    <th class="ps-4 fw-600 py-3 border-0 text-uppercase text-secondary">{{ __('Month') }}</th>
                                    <th class="text-end fw-600 py-3 border-0 text-uppercase text-secondary">{{ __('Assets') }}</th>
                                    <th class="text-end fw-600 py-3 border-0 text-uppercase text-secondary">{{ __('Debt') }}</th>
                                    <th class="text-end pe-4 fw-600 py-3 border-0 text-uppercase text-secondary">{{ __('Net') }}</th>
                                </tr>
                            </thead>
                            <tbody id="netWorthTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="netWorthDetailModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-0 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-body-tertiary">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-0 me-3">
                        <i class="bi bi-journal-text fs-4 text-primary"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="netWorthDetailTitle">Details</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="netWorthDetailList" class="custom-scrollbar net-worth-detail-list"></div>
            </div>
            <div class="modal-footer border-0 p-3 bg-body-tertiary">
                <button type="button" class="btn btn-secondary rounded-0 px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
