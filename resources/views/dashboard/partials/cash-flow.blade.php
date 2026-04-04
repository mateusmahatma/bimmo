<div class="card card-dashboard border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">
                {{ __('Cash Flow') }}
                <span class="text-muted fw-normal ms-1" id="cashFlowPeriodeLabel" style="font-size: 0.8rem;">
                    ({{ $periode }} {{ __('months ago') }})
                </span>
            </h5>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Monitor your income and expenses over time.') }}</p>
        </div>
        <select id="filterPeriode" name="periode" class="form-select form-select-sm rounded-0" style="width:120px">
            <option value="2"  {{ $periode == 2  ? 'selected' : '' }}>2 {{ __('Months') }}</option>
            <option value="6"  {{ $periode == 6  ? 'selected' : '' }}>6 {{ __('Months') }}</option>
            <option value="12" {{ $periode == 12 ? 'selected' : '' }}>12 {{ __('Months') }}</option>
        </select>
    </div>
    <div class="card-body p-3 p-md-4">
        <div id="cashflowChart" style="height: 300px;"></div>
        <div id="cashflowTableContainer" class="table-responsive mt-3">
            @include('dashboard.partials.cashflow-table', ['cashflow' => $cashflow])
        </div>
    </div>
</div>
