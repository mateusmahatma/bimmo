<div class="card card-dashboard border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">{{ __('Budget Performance') }}</h5>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Track your budget usage.') }}</p>
        </div>
        <select id="filterTanggal" class="form-select form-select-sm rounded-0" style="width: 180px;">
            <option value="">{{ __('All Data') }}</option>
            @foreach($filterOptions as $row)
                <option value="{{ $row->tanggal_mulai }}_{{ $row->tanggal_selesai }}">
                    {{ $row->tanggal_mulai }} s/d {{ $row->tanggal_selesai }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="card-body p-3 p-md-4">
        @include('dashboard.partials.anggaran')
        @stack('anggaran-css')
        @stack('anggaran.scripts')
    </div>
</div>
