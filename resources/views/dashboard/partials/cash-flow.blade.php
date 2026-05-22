<div class="card shadow-none">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark">
                {{ __('Cash Flow') }}
                <span class="text-muted fw-normal ms-1" id="cashFlowPeriodeLabel">
                    ({{ $periode }} {{ __('months ago') }})
                </span>
            </h5>
            <p class="text-muted mb-0 mt-1">
                {{ __('Monitor your income and expenses over time.') }}</p>
        </div>
        <select id="filterPeriode" name="periode" class="form-select form-select-sm rounded-0" style="width:120px">
            <option value="2" {{ $periode == 2 ? 'selected' : '' }}>2 {{ __('Months') }}</option>
            <option value="6" {{ $periode == 6 ? 'selected' : '' }}>6 {{ __('Months') }}</option>
            <option value="12" {{ $periode == 12 ? 'selected' : '' }}>12 {{ __('Months') }}</option>
        </select>
    </div>
    <div class="card-body p-3 p-md-4">
        @include('dashboard.partials.cash-flow-content')
    </div>
</div>
