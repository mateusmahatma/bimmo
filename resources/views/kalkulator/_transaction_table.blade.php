<div class="table-responsive">
    @php
        $currentSort = request('sort', 'tgl_transaksi');
        $currentDir = request('direction', 'asc');

        $sortLink = function ($column) {
            $dir = request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
        };
    @endphp
    <table id="detailAnggaranTable" class="table table-hover align-middle mb-0" style="width:100%">
        <thead class="bg-light">
            <tr style="border-bottom: 2px solid #edf2f9;">
                <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('No') }}</th>
                <th class="text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('tgl_transaksi') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1 sort-link" data-sort="tgl_transaksi" data-direction="{{ $currentSort === 'tgl_transaksi' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('Date') }} @if ($currentSort === 'tgl_transaksi') <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </a>
                </th>
                <th class="text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('nominal') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1 sort-link" data-sort="nominal" data-direction="{{ $currentSort === 'nominal' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('Expense Category') }} @if ($currentSort === 'nominal') <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </a>
                </th>
                <th class="text-end text-secondary small text-uppercase fw-bold py-3">{{ __('Amount') }}</th>
                <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Description') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $row)
                <tr>
                    <td class="text-center text-secondary fw-medium" data-label="{{ __('No') }}">{{ $loop->iteration + ($transaksi->currentPage() - 1) * $transaksi->perPage() }}</td>
                    <td class="text-dark" data-label="{{ __('Date') }}">
                        {{ \Carbon\Carbon::parse($row->tgl_transaksi)->locale(app()->getLocale())->isoFormat('D MMM Y') }}
                    </td>
                    <td class="fw-semibold text-dark" data-label="{{ __('Category') }}">
                        {{ $row->pengeluaranRelation->nama ?? '-' }}
                    </td>
                    <td class="text-end fw-bold text-dark" data-label="{{ __('Amount') }}">
                        Rp {{ number_format($row->nominal, 0, ',', '.') }}
                    </td>
                    <td data-label="{{ __('Description') }}">
                        @if($row->keterangan)
                            @php
                                $items = explode("\n", $row->keterangan);
                                $items = array_filter(array_map('trim', $items));
                            @endphp
                            @if(count($items) > 1)
                                <ol class="mb-0 ps-3 small text-muted">
                                    @foreach($items as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ol>
                            @else
                                <span class="text-muted small">{{ $items[0] ?? '-' }}</span>
                            @endif
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">{{ __('No related transactions found for this period.') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-4 pt-3 border-top px-3">
    {{ $transaksi->links('pagination::bootstrap-5') }}
</div>