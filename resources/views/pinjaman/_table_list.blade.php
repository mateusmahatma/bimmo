<div class="table-responsive">
    @php
        $currentSort = request('sort', 'created_at');
        $currentDir = request('direction', 'desc');

        $sortLink = function ($column) {
            $dir = request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
        };
    @endphp
    <table id="pinjamanTable" class="table table-hover table-borderless align-middle" style="width:100%">
        <thead class="table-light">
            <tr>
                <th style="width: 5%;" class="text-center">
                    <input class="form-check-input" type="checkbox" id="checkAll" style="cursor: pointer;">
                </th>
                <th style="width: 5%;" class="text-secondary small fw-bold">{{ __('NO') }}</th>
                <th class="text-secondary small  fw-bold">
                    <a href="{{ $sortLink('nama_pinjaman') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1 sort-link"
                        data-sort="nama_pinjaman"
                        data-direction="{{ $currentSort === 'nama_pinjaman' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('LOAN NAME') }}
                        @if ($currentSort === 'nama_pinjaman')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="text-secondary small fw-bold">{{ __('NOTES') }}</th>
                <th class="text-center text-secondary small fw-bold">
                    <a href="{{ $sortLink('next_due_date') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1 sort-link"
                        data-sort="next_due_date"
                        data-direction="{{ $currentSort === 'next_due_date' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('DUE DATE') }}
                        @if ($currentSort === 'next_due_date')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="bi bi-arrow-down-up opacity-50"></i>
                        @endif
                    </a>
                </th>
                <th class="text-end text-secondary small fw-bold">
                    <a href="{{ $sortLink('jumlah_angsuran') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center justify-content-end gap-1 sort-link"
                        data-sort="jumlah_angsuran"
                        data-direction="{{ $currentSort === 'jumlah_angsuran' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('MONTHLY PAYMENTS') }}
                        @if ($currentSort === 'jumlah_angsuran')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="bi bi-arrow-down-up opacity-50"></i>
                        @endif
                    </a>
                </th>
                <th class="text-end text-secondary small fw-bold">
                    <a href="{{ $sortLink('total_loan') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center justify-content-end gap-1 sort-link"
                        data-sort="total_loan"
                        data-direction="{{ $currentSort === 'total_loan' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('TOTAL LOAN') }}
                        @if ($currentSort === 'total_loan')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="bi bi-arrow-down-up opacity-50"></i>
                        @endif
                    </a>
                </th>
                <th class="text-end text-secondary small fw-bold">
                    <a href="{{ $sortLink('paid_amount') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center justify-content-end gap-1 sort-link"
                        data-sort="paid_amount"
                        data-direction="{{ $currentSort === 'paid_amount' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('PAID AMOUNT') }}
                        @if ($currentSort === 'paid_amount')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="bi bi-arrow-down-up opacity-50"></i>
                        @endif
                    </a>
                </th>
                <th class="text-secondary small fw-bold text-center">{{ __('PROGRESS') }}</th>
                <th class="text-end text-secondary small fw-bold">
                    <a href="{{ $sortLink('jumlah_pinjaman') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center justify-content-end gap-1 sort-link"
                        data-sort="jumlah_pinjaman"
                        data-direction="{{ $currentSort === 'jumlah_pinjaman' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('REMAINING BALANCE') }}
                        @if ($currentSort === 'jumlah_pinjaman')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="bi bi-arrow-down-up opacity-50"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center text-secondary small fw-bold">
                    <a href="{{ $sortLink('status') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1 sort-link"
                        data-sort="status"
                        data-direction="{{ $currentSort === 'status' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('STATUS') }}
                        @if ($currentSort === 'status')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="bi bi-arrow-down-up opacity-50"></i>
                        @endif
                    </a>
                </th>
                <th style="width: 10%;" class="text-center text-secondary small fw-bold">{{ __('ACTION') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pinjaman as $row)
                @php
                    $hash = Vinkla\Hashids\Facades\Hashids::encode($row->id);
                    $row->hash = $hash;
                    $paid = $row->bayar_pinjaman->sum('jumlah_bayar');
                    $total_loan = $row->jumlah_pinjaman + $paid;
                    $progressPercent = $total_loan > 0 ? (int) round(($paid / $total_loan) * 100) : 0;
                    $progressPercent = max(0, min(100, $progressPercent));
                    $progressClass =
                        $progressPercent >= 100
                            ? 'bg-success'
                            : ($progressPercent >= 75
                                ? 'bg-info'
                                : ($progressPercent >= 50
                                    ? 'bg-primary'
                                    : 'bg-warning'));
                @endphp
                <tr>
                    <td class="text-center">
                        <input class="form-check-input check-item" type="checkbox" value="{{ $hash }}"
                            style="cursor: pointer;">
                    </td>
                    <td class="text-secondary fw-medium">
                        {{ $loop->iteration + ($pinjaman->currentPage() - 1) * $pinjaman->perPage() }}</td>
                    <td class="fw-semibold text-dark">{{ $row->nama_pinjaman }}</td>
                    <td class="text-muted small rich-text-index">{!! $row->keterangan ?: '-' !!}</td>
                    <td class="text-center fw-semibold text-dark">
                        @php
                            $totalPaidForDue = $row->bayar_pinjaman->sum('jumlah_bayar');
                            $cumulativeExpected = 0;
                            $nextDueDate = null;
                            $isEndDateFallback = false;
                            $simulasiList = $row->simulasi_cicilan ?? [];

                            if (empty($simulasiList)) {
                                $nextDueDate = $row->end_date ?? null;
                                $isEndDateFallback = (bool) $nextDueDate;
                            } else {
                                foreach ($simulasiList as $simulasi) {
                                    $cumulativeExpected += (float) ($simulasi['nominal'] ?? 0);
                                    $isPaid = $totalPaidForDue >= $cumulativeExpected - 0.01;
                                    if (!$isPaid) {
                                        $nextDueDate = $simulasi['tanggal'] ?? null;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        @if ($nextDueDate)
                            @php
                                $nextDueCarbon = \Carbon\Carbon::parse($nextDueDate)->startOfDay();
                                $daysLeft = now()->startOfDay()->diffInDays($nextDueCarbon, false);
                            @endphp
                            {{ $nextDueCarbon->translatedFormat('d M Y') }}
                            @if ($isEndDateFallback)
                                <div class="text-muted small fw-normal">{{ __('End Date') }}</div>
                            @endif
                            <div class="text-danger small fw-normal">
                                @if ($daysLeft > 0)
                                    {{ $daysLeft }} {{ __('hari lagi') }}
                                @elseif($daysLeft === 0)
                                    {{ __('hari ini') }}
                                @else
                                    {{ __('terlambat') }} {{ abs($daysLeft) }} {{ __('hari') }}
                                @endif
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end fw-bold text-warning">Rp
                        {{ number_format($row->nominal_angsuran, 0, ',', '.') }}
                    </td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($total_loan, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold text-success">Rp {{ number_format($paid, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if ($total_loan > 0)
                            <div class="loan-progress-wrap mx-auto">
                                <div class="progress loan-progress" role="progressbar"
                                    aria-label="{{ __('Loan payoff progress') }}"
                                    aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar {{ $progressClass }}"
                                        style="width: {{ $progressPercent }}%"></div>
                                </div>
                                <div class="loan-progress-text small text-muted mt-1">{{ $progressPercent }}%</div>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end fw-bold text-danger">Rp {{ number_format($row->jumlah_pinjaman, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if ($row->status === 'lunas')
                            <span class="badge bg-success-light text-success"><i class="bi bi-check-circle me-1"></i>
                                {{ __('Paid in full') }}</span>
                        @else
                            <span class="badge bg-danger-light text-danger"><i class="bi bi-x-circle me-1"></i>
                                {{ __('Not Yet Paid in Full') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @include('pinjaman.tombol', ['pinjaman' => $row])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center py-5">
                        <div class="py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">{{ __('No Loan records found') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-4 pt-3 border-top px-3">
    {{ $pinjaman->links('pagination::bootstrap-5') }}
</div>
