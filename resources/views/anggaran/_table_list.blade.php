@php
    $currentSort = $sort ?? 'created_at';
    $currentDirection = $direction ?? 'desc';

    function getSortIcon($column, $currentSort, $currentDirection) {
        if ($currentSort == $column) {
            return $currentDirection == 'asc' ? '<i class="bi bi-arrow-up small"></i>' : '<i class="bi bi-arrow-down small"></i>';
        }
        return '<i class="bi bi-arrow-down-up small opacity-50"></i>';
    }
@endphp

<table id="anggaranTable" class="table table-hover align-middle mb-0" style="width:100%">
    <thead class="bg-light">
        <tr style="border-bottom: 2px solid #edf2f9;">
            <th style="width: 5%;" class="text-center py-3">
                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="checkAll" style="cursor: pointer;">
                </div>
            </th>
            <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3 d-none d-md-table-cell">No</th>
            <th class="text-secondary small text-uppercase fw-bold py-3">
                <a href="javascript:void(0)" class="sort-link text-secondary" data-sort="nama_anggaran" data-direction="{{ ($currentSort == 'nama_anggaran' && $currentDirection == 'asc') ? 'desc' : 'asc' }}">
                    {{ __('Budget Name') }} {!! getSortIcon('nama_anggaran', $currentSort, $currentDirection) !!}
                </a>
            </th>
            <th class="text-secondary small text-uppercase fw-bold py-3 text-center">
                <a href="javascript:void(0)" class="sort-link text-secondary" data-sort="persentase_anggaran" data-direction="{{ ($currentSort == 'persentase_anggaran' && $currentDirection == 'asc') ? 'desc' : 'asc' }}">
                    {{ __('Percentage') }} {!! getSortIcon('persentase_anggaran', $currentSort, $currentDirection) !!}
                </a>
            </th>
            <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Expense Types') }}</th>
            <th class="text-center text-secondary small text-uppercase fw-bold py-3 d-none d-md-table-cell">
                <a href="javascript:void(0)" class="sort-link text-secondary" data-sort="created_at" data-direction="{{ ($currentSort == 'created_at' && $currentDirection == 'asc') ? 'desc' : 'asc' }}">
                    {{ __('Created At') }} {!! getSortIcon('created_at', $currentSort, $currentDirection) !!}
                </a>
            </th>
            <th class="text-center text-secondary small text-uppercase fw-bold py-3 d-none d-md-table-cell">
                <a href="javascript:void(0)" class="sort-link text-secondary" data-sort="updated_at" data-direction="{{ ($currentSort == 'updated_at' && $currentDirection == 'asc') ? 'desc' : 'asc' }}">
                    {{ __('Last Updated') }} {!! getSortIcon('updated_at', $currentSort, $currentDirection) !!}
                </a>
            </th>
            <th style="width: 10%;" class="text-center text-secondary small text-uppercase fw-bold py-3">{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($anggarans as $index => $item)
            <tr>
                <td class="text-center">
                    <input class="form-check-input check-item" type="checkbox" value="{{ $item->id_anggaran }}">
                </td>
                <td class="text-center d-none d-md-table-cell text-muted small">
                    {{ $anggarans->firstItem() + $index }}
                </td>
                <td>
                    <div class="fw-bold text-dark">{{ $item->nama_anggaran }}</div>
                </td>
                <td class="text-center">
                    <span class="badge bg-primary-light text-primary rounded-pill px-3">{{ $item->persentase_anggaran }}%</span>
                </td>
                <td>
                    @php
                        $pengeluaranNames = \App\Models\Pengeluaran::whereIn('id', (array)$item->id_pengeluaran)->pluck('nama')->toArray();
                        $displayNames = array_slice($pengeluaranNames, 0, 3);
                        $remainingCount = count($pengeluaranNames) - 3;
                    @endphp
                    @foreach ($displayNames as $name)
                        <span class="badge bg-light text-dark border me-1 mb-1 small">{{ $name }}</span>
                    @endforeach
                    @if ($remainingCount > 0)
                        <span class="badge bg-light text-muted border mb-1 small">+{{ $remainingCount }} more</span>
                    @endif
                    @if (empty($pengeluaranNames))
                        <span class="text-muted small">-</span>
                    @endif
                </td>
                <td class="text-center d-none d-md-table-cell text-muted small">
                    <span style="font-family: 'Consolas', monospace;">{{ $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '-' }}</span>
                </td>
                <td class="text-center d-none d-md-table-cell text-muted small">
                    <span style="font-family: 'Consolas', monospace;">{{ $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '-' }}</span>
                </td>
                <td class="text-center">
                    @include('anggaran.tombol', ['request' => $item])
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-clipboard-x fs-1 opacity-25"></i>
                        <p class="mt-2 mb-0">{{ __('No budget categories found.') }}</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if ($anggarans->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $anggarans->firstItem() }} to {{ $anggarans->lastItem() }} of {{ $anggarans->total() }} entries
            </div>
            <div class="ajax-pagination">
                {!! $anggarans->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
@endif
