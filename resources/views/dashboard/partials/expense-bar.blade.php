<div class="card card-dashboard border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">{{ __('Expense Bar') }}</h5>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Expenses breakdown by category.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <select id="filterBulan" name="bulan" class="form-select form-select-sm rounded-0" style="width: 110px;">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select id="filterTahun" name="tahun" class="form-select form-select-sm rounded-0" style="width: 80px;">
                @for ($y = now()->year - 5; $y <= now()->year; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="expenseBarTableContainer">
            @include('dashboard.partials.expense-bar-table', ['pengeluaranKategori' => $pengeluaranKategori])
        </div>
        <div class="px-4 py-3 border-top {{ $uiStyle === 'milenial' ? 'bg-transparent' : 'card-footer-strip' }}">
            <div class="d-flex justify-content-between align-items-center">
                <span class="section-label">{{ __('Total Expenditures') }}</span>
                <strong id="totalPengeluaranValue" class="text-dark">Rp {{ number_format((float) $totalPengeluaranBulan, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>
</div>
