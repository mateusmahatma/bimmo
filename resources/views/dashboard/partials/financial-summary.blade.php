<div class="card card-dashboard border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">
                    {{ __('Financial Summary') }}
                </h5>
                @if($totalNominalSisa > 0)
                    <span class="badge bg-success" style="font-size: 0.65rem;">{{ __('Surplus') }}</span>
                @elseif($totalNominalSisa < 0)
                    <span class="badge bg-danger" style="font-size: 0.65rem;">{{ __('Deficit') }}</span>
                @endif
            </div>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Overview of your financial status this month.') }}</p>
        </div>
        <button id="toggleNominalBtn"
            class="btn {{ $uiStyle === 'milenial' ? 'btn-light border-0' : 'btn-outline-secondary' }} btn-sm rounded-0 px-3"
            data-url="{{ route('dashboard.toggle-nominal.ajax') }}">
            <i class="bi {{ $showNominal ? 'bi-eye-slash' : 'bi-eye' }}"></i>
        </button>
    </div>

    <div class="card-body p-3 p-md-4">
        <div class="row g-3">
            <div class="col-12 col-xl-4">
                <div class="{{ $uiStyle === 'milenial' ? 'm-hero-balance' : 'card-hero-balance' }} h-100 rounded-0 p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="section-label mb-0" style="color: rgba(255,255,255,0.65);">{{ __('Current Balance') }}</p>
                            <div class="icon-circle balance">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                        <h2 class="mb-1 fw-extrabold text-white stat-number-lg" id="summary-saldo">{{ $saldoView }}</h2>
                    </div>
                    <div class="mt-2 growth-detail-trigger"
                         style="cursor: pointer;"
                         data-title="{{ __('Balance Change') }}"
                         data-current="{{ round($numbers['saldo'], 0) }}"
                         data-last="{{ round($saldoLalu, 0) }}"
                         data-percent="{{ $persenSaldo }}"
                         data-type="balance">
                        @if($persenSaldo > 0)
                            <span class="text-white fw-bold" style="font-size:0.82rem;"><i class="bi bi-arrow-up-short"></i>{{ abs($persenSaldo) }}%</span>
                        @elseif($persenSaldo < 0)
                            <span class="text-white fw-bold" style="font-size:0.82rem;"><i class="bi bi-arrow-down-short"></i>{{ abs($persenSaldo) }}%</span>
                        @else
                            <span class="text-white opacity-50" style="font-size:0.82rem;"><i class="bi bi-dash"></i> 0%</span>
                        @endif
                        <span class="text-white opacity-50 ms-1" style="font-size: 0.78rem;">{{ __('vs last month') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-8">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card border-0 shadow-none h-100 {{ $uiStyle === 'milenial' ? 'm-glass-container' : 'stat-card-inner' }}" style="border-radius: 12px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <p class="section-label mb-0">{{ __('Total Income') }}</p>
                                    <div class="icon-circle income">
                                        <i class="bi bi-graph-up-arrow"></i>
                                    </div>
                                </div>
                                <h4 class="mb-1 fw-bold text-success stat-number-md" id="summary-pemasukan">{{ $pemasukanView }}</h4>
                                <div class="growth-detail-trigger"
                                     style="cursor: pointer;"
                                     data-title="{{ __('Income Change') }}"
                                     data-current="{{ round($numbers['pemasukan'], 0) }}"
                                     data-last="{{ round($pemasukanLalu, 0) }}"
                                     data-percent="{{ $persenPemasukan }}"
                                     data-type="income">
                                    @if($persenPemasukan > 0)
                                        <span class="text-success fw-bold stat-vs-label"><i class="bi bi-arrow-up-short"></i>{{ abs($persenPemasukan) }}%</span>
                                    @elseif($persenPemasukan < 0)
                                        <span class="text-danger fw-bold stat-vs-label"><i class="bi bi-arrow-down-short"></i>{{ abs($persenPemasukan) }}%</span>
                                    @else
                                        <span class="text-muted stat-vs-label"><i class="bi bi-dash"></i> 0%</span>
                                    @endif
                                    <span class="stat-vs-label ms-1">{{ __('vs last month') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card border-0 shadow-none h-100 {{ $uiStyle === 'milenial' ? 'm-glass-container' : 'stat-card-inner' }}" style="border-radius: 12px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <p class="section-label mb-0">{{ __('Total Expense') }}</p>
                                    <div class="icon-circle expense">
                                        <i class="bi bi-graph-down-arrow"></i>
                                    </div>
                                </div>
                                <h4 class="mb-1 fw-bold text-danger stat-number-md" id="summary-pengeluaran">{{ $pengeluaranView }}</h4>
                                <div class="growth-detail-trigger"
                                     style="cursor: pointer;"
                                     data-title="{{ __('Expense Change') }}"
                                     data-current="{{ round($numbers['pengeluaran'], 0) }}"
                                     data-last="{{ round($pengeluaranLalu, 0) }}"
                                     data-percent="{{ $persenPengeluaran }}"
                                     data-type="expense">
                                    @if($persenPengeluaran > 0)
                                        <span class="text-danger fw-bold stat-vs-label"><i class="bi bi-arrow-up-short"></i>{{ abs($persenPengeluaran) }}%</span>
                                    @elseif($persenPengeluaran < 0)
                                        <span class="text-success fw-bold stat-vs-label"><i class="bi bi-arrow-down-short"></i>{{ abs($persenPengeluaran) }}%</span>
                                    @else
                                        <span class="text-muted stat-vs-label"><i class="bi bi-dash"></i> 0%</span>
                                    @endif
                                    <span class="stat-vs-label ms-1">{{ __('vs last month') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card border-0 shadow-none h-100 {{ $uiStyle === 'milenial' ? 'm-glass-container' : 'stat-card-inner' }}" style="border-radius: 12px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <p class="section-label mb-0">{{ __('Expense Today') }}</p>
                                    <div class="icon-circle today">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                </div>
                                <h4 class="mb-1 fw-bold text-dark stat-number-md" id="summary-hari-ini">{{ $pengeluaranHariIni }}</h4>
                                <span class="stat-vs-label">{{ __('Daily monitoring') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card border-0 shadow-none h-100 {{ $uiStyle === 'milenial' ? 'm-glass-container' : 'stat-card-inner' }}" style="border-radius: 12px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <p class="section-label mb-0">{{ __('Next Installment') }}</p>
                                    <div class="icon-circle install">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                </div>
                                <h4 class="mb-1 fw-bold text-primary stat-number-md" id="summary-cicilan-besok">{{ $cicilanBesokView }}</h4>
                                <span class="stat-vs-label">{{ __('Repayment goal') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
