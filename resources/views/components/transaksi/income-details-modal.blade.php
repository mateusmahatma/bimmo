@props(['summaryPemasukan', 'totalPemasukan'])

<div class="modal fade" id="incomeDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-success fw-bold">{{ __('Income Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2" id="income-modal-body">
                <ul class="list-group list-group-flush">
                    @forelse($summaryPemasukan as $row)
                        @php
                            $percentage = $totalPemasukan > 0 ? ($row->total / $totalPemasukan) * 100 : 0;
                        @endphp
                        <li class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium text-dark">{{ $row->pemasukanRelation->nama ?? 'Others' }}</span>
                                <span class="fw-bold text-success small">Rp {{ number_format($row->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="progress transaksi-progress-thin">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-end text-muted transaksi-pct-text">{{ number_format($percentage, 1) }}%</div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-3">{{ __('No data available') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
