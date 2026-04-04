<div class="card card-dashboard border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">{{ __("Today's Transactions") }}</h5>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dashboard {{ $uiStyle === 'milenial' ? 'table-borderless' : '' }} align-middle mb-0">
                @if($uiStyle !== 'milenial')
                    <thead>
                        <tr class="table-header-strip">
                            <th class="px-4 py-3 section-label border-bottom" style="font-size: 0.68rem;">{{ __('Time') }}</th>
                            <th class="py-3 section-label border-bottom" style="font-size: 0.68rem;">{{ __('Type') }}</th>
                            <th class="py-3 section-label border-bottom" style="font-size: 0.68rem;">{{ __('Explanation') }}</th>
                            <th class="py-3 pe-4 text-end section-label border-bottom" style="font-size: 0.68rem;">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                @endif
                <tbody>
                    @forelse ($transaksiHariIni as $row)
                        <tr class="{{ $uiStyle === 'milenial' ? 'm-list-item' : '' }}">
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($row->waktu)->format('H:i') }}</div>
                                @if($uiStyle === 'milenial')
                                    <div class="stat-vs-label">{{ $row->kategori }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge-type {{ $row->jenis === 'pemasukan' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                    {{ ucfirst($row->jenis) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{!! $row->keterangan ?? '-' !!}</div>
                                @if($uiStyle !== 'milenial')
                                    <div class="stat-vs-label">{{ $row->kategori }}</div>
                                @endif
                            </td>
                            <td class="text-end pe-4 fw-bold {{ $row->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                {{ $row->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format((float) $row->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                {{ __('No transactions today') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-3 {{ $uiStyle !== 'milenial' ? 'card-footer-strip' : '' }}">
            <div class="d-flex align-items-center gap-2">
                <div class="icon-circle income" style="width: 32px; height: 32px; font-size: 0.95rem; border-radius: 8px;">
                    <i class="bi bi-arrow-down-left"></i>
                </div>
                <span class="text-success fw-semibold" style="font-size: 0.85rem;">
                    {{ __('Income') }}: <strong>Rp {{ number_format((float) $totalMasukHariIni, 0, ',', '.') }}</strong>
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="icon-circle expense" style="width: 32px; height: 32px; font-size: 0.95rem; border-radius: 8px;">
                    <i class="bi bi-arrow-up-right"></i>
                </div>
                <span class="text-danger fw-semibold" style="font-size: 0.85rem;">
                    {{ __('Expenses') }}: <strong>Rp {{ number_format((float) $totalKeluarHariIni, 0, ',', '.') }}</strong>
                </span>
            </div>
        </div>
    </div>
</div>
