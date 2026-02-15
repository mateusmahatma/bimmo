<div class="table-responsive">
    @php
        $currentSort = request('sort');
        $currentDir = request('direction', 'desc');

        // Use anonymous function to prevent redeclaration error
        $sortLink = function ($column) {
            $dir = request('direction') === 'asc' ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
        };
    @endphp
    <table id="transaksiTable" class="table table-hover align-middle mb-0" style="width:100%">
        <thead class="bg-light">
            <tr style="border-bottom: 2px solid #edf2f9;">
                <th style="width: 5%;" class="text-center py-3">
                    <div class="form-check d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" id="checkAll" style="cursor: pointer;">
                    </div>
                </th>
                <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3">No</th>
                <th style="width: 15%;" class="text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('tgl_transaksi') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1 sort-link"
                        data-sort="tgl_transaksi"
                        data-direction="{{ $currentSort === 'tgl_transaksi' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        Date @if ($currentSort === 'tgl_transaksi')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th style="width: 20%;" class="text-secondary small text-uppercase fw-bold py-3">Category</th>
                <th class="text-secondary small text-uppercase fw-bold py-3">Description</th>
                <th style="width: 15%;" class="text-end text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('nominal_pemasukan') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center justify-content-end gap-1 sort-link"
                        data-sort="nominal_pemasukan"
                        data-direction="{{ $currentSort === 'nominal_pemasukan' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        Amount @if ($currentSort === 'nominal_pemasukan' || $currentSort === 'nominal')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th style="width: 10%;" class="text-center text-secondary small text-uppercase fw-bold py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $row)
                <tr>
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input check-item" type="checkbox" value="{{ $row->id }}">
                        </div>
                    </td>
                    <td>{{ $loop->iteration + ($transaksi->currentPage() - 1) * $transaksi->perPage() }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($row->tgl_transaksi)->format('d M Y') }}
                        </div>
                        <div class="small text-muted">
                            {{ \Carbon\Carbon::parse($row->tgl_transaksi)->locale('en')->isoFormat('dddd') }}</div>
                    </td>
                    <td>
                        @if ($row->nominal_pemasukan > 0)
                            <span
                                class="badge bg-success-light text-success border border-success-subtle rounded-pill px-3">
                                <i class="bi bi-arrow-down-left me-1"></i> {{ $row->pemasukanRelation?->nama ?? 'Income' }}
                            </span>
                        @else
                            <span class="badge bg-danger-light text-danger border border-danger-subtle rounded-pill px-3">
                                <i class="bi bi-arrow-up-right me-1"></i>
                                {{ $row->pengeluaranRelation?->nama ?? 'Expense' }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if ($row->keterangan)
                            <div class="text-muted small" style="max-height: 60px; overflow-y: auto;">
                                @php
                                    $descLines = array_filter(preg_split('/\r\n|\r|\n/', $row->keterangan), function (
                                        $l,
                                    ) {
                                        return trim($l) !== '';
                                    });
                                @endphp
                                @if (count($descLines) > 1)
                                    <ol class="ps-3 mb-0">
                                        @foreach ($descLines as $line)
                                            <li>{{ trim($line) }}</li>
                                        @endforeach
                                    </ol>
                                @else
                                    {!! nl2br(e($row->keterangan)) !!}
                                @endif
                            </div>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td
                        class="text-end fw-bold {{ $row->nominal_pemasukan > 0 ? 'text-success' : 'text-danger' }}">
                        @if ($row->nominal_pemasukan > 0)
                            + Rp {{ number_format($row->nominal_pemasukan, 0, ',', '.') }}
                        @else
                            - Rp {{ number_format($row->nominal, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-center">
                        @include('transaksi._aksi', ['row' => $row])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="py-4">
                            <img src="{{ asset('img/no-data.svg') }}" alt="No Data" style="width: 80px; opacity: 0.5;"
                                class="mb-3">
                            <p class="text-muted mb-1">No transaction data found.</p>
                            <a href="{{ route('transaksi.create') }}"
                                class="btn btn-primary btn-sm rounded-pill mt-2">Add Transaction</a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<div class="d-flex justify-content-end mt-4 pt-3 border-top">
    {{ $transaksi->withQueryString()->links('pagination::bootstrap-5') }}
</div>
