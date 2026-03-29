@if($groupedByDate->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-calendar-x fs-1 text-muted opacity-50"></i>
        <p class="text-muted mt-3 mb-0">{{ __('No transactions found for this period.') }}</p>
        <p class="small text-muted">{{ __('Try adjusting the date filter above.') }}</p>
    </div>
@else
    <div class="date-cards-list">
        @foreach($groupedByDate as $group)
            @php
                $net = $group->totalPemasukan - $group->totalPengeluaran;
                $netClass = $net >= 0 ? 'text-success' : 'text-danger';
                $netIcon  = $net >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right';
            @endphp
            <a href="{{ route('transaksi.byDate', $group->date) }}"
               class="date-card-link text-decoration-none d-block mb-3">
                <div class="date-card p-3 d-flex align-items-center gap-3">

                    {{-- Calendar Badge --}}
                    <div class="date-badge flex-shrink-0 text-center">
                        <div class="date-badge-month">{{ \Carbon\Carbon::parse($group->date)->format('M') }}</div>
                        <div class="date-badge-day">{{ \Carbon\Carbon::parse($group->date)->format('d') }}</div>
                        <div class="date-badge-dow">{{ \Carbon\Carbon::parse($group->date)->translatedFormat('D') }}</div>
                    </div>

                    {{-- Info --}}
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                            <span class="fw-semibold text-dark date-card-title">{{ $group->dateFormatted }}</span>
                            <span class="badge bg-light text-secondary border fw-normal rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                {{ $group->count }} {{ __('transactions') }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-3 mt-2">
                            @if($group->totalPemasukan > 0)
                            <span class="small fw-medium text-success">
                                <i class="bi bi-arrow-down-circle me-1"></i>
                                Rp {{ number_format($group->totalPemasukan, 0, ',', '.') }}
                            </span>
                            @endif
                            @if($group->totalPengeluaran > 0)
                            <span class="small fw-medium text-danger">
                                <i class="bi bi-arrow-up-circle me-1"></i>
                                Rp {{ number_format($group->totalPengeluaran, 0, ',', '.') }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Net + Chevron --}}
                    <div class="flex-shrink-0 text-end">
                        <div class="fw-bold {{ $netClass }} small">
                            <i class="bi {{ $netIcon }} me-1"></i>
                            Rp {{ number_format(abs($net), 0, ',', '.') }}
                        </div>
                        <i class="bi bi-chevron-right text-muted mt-1 d-block" style="font-size: 0.8rem;"></i>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif
