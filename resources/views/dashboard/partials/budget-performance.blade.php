@push('css')
<link rel="stylesheet" href="{{ asset('css/dashboard/anggaran.css') }}?v={{ filemtime(public_path('css/dashboard/anggaran.css')) }}">
@endpush

<div class="card card-dashboard border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">{{ __('Budget Performance') }}</h5>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Track your budget usage.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button id="btnSyncAnggaran" class="btn btn-sm btn-outline-primary border-0 rounded-0" title="{{ __('Sync Data') }}">
                <i class="bi bi-arrow-repeat"></i>
            </button>
            <select id="filterTanggal" class="form-select form-select-sm rounded-0" style="width: 180px;">
                <option value="">{{ __('All Data') }}</option>
                @foreach($filterOptions as $row)
                    <option value="{{ $row->tanggal_mulai }}_{{ $row->tanggal_selesai }}">
                        {{ $row->tanggal_mulai }} s/d {{ $row->tanggal_selesai }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body p-3 p-md-4">
        @include('dashboard.partials.budget-performance-content')
    </div>
</div>

@push('scripts')
<script>
    window.dashboardBudgetConfig = {
        uiStyle: @json($uiStyle),
        chartUrl: "{{ route('anggaran.chart') }}",
        syncUrl: "{{ route('dashboard.sync-anggaran') }}",
        csrfToken: "{{ csrf_token() }}",
        labels: {
            noData: @json(__('Tidak ada data anggaran.')),
            noBurnRateData: @json(__('Pilih periode awal/sedang berjalan untuk melihat burn rate.')),
            burnRateSummary: @json(__('Burn Rate Summary')),
            selectedPeriod: @json(__('Berdasarkan Periode Terpilih')),
            overBudget: @json(__('Over Budget')),
            warning: @json(__('Waspada')),
            safe: @json(__('Aman')),
            highestExpense: @json(__('Pengeluaran Terboros')),
            spentLabel: @json(__('Terpakai')),
            budgetLabel: @json(__('Budget')),
            remainingDays: @json(__('Sisa Waktu')),
            daysSuffix: @json(__('Hari')),
            detail: @json(__('Detail')),
            success: @json(__('Success')),
        },
    };
</script>
<script src="{{ asset('js/dashboard-budget-performance.js') }}?v={{ filemtime(public_path('js/dashboard-budget-performance.js')) }}"></script>
@endpush
