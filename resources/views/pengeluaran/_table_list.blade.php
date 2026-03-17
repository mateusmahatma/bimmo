<div class="table-responsive">
    @php
        $currentSort = request('sort', 'created_at');
        $currentDir = request('direction', 'desc');

        $sortLink = function ($column) {
            $dir = request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
        };
    @endphp
    <table id="pengeluaranTable" class="table table-hover align-middle mb-0" style="width:100%">
        <thead class="bg-light">
            <tr style="border-bottom: 2px solid #edf2f9;">
                <th style="width: 5%;" class="text-center py-3">
                    <div class="form-check d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" id="checkAll" style="cursor: pointer;">
                    </div>
                </th>
                <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3">{{ __('No') }}</th>
                <th class="text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('nama') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1 sort-link"
                        data-sort="nama"
                        data-direction="{{ $currentSort === 'nama' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('Category Name') }} @if ($currentSort === 'nama')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('created_at') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1 sort-link"
                        data-sort="created_at"
                        data-direction="{{ $currentSort === 'created_at' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('Created At') }} @if ($currentSort === 'created_at')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('updated_at') }}"
                        class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1 sort-link"
                        data-sort="updated_at"
                        data-direction="{{ $currentSort === 'updated_at' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('Last Updated') }} @if ($currentSort === 'updated_at')
                            <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th style="width: 10%;" class="text-center text-secondary small text-uppercase fw-bold py-3">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengeluaran as $row)
                <tr>
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input check-item" type="checkbox" value="{{ $row->id }}" style="cursor: pointer;">
                        </div>
                    </td>
                    <td class="text-center text-secondary fw-medium">{{ $loop->iteration + ($pengeluaran->currentPage() - 1) * $pengeluaran->perPage() }}</td>
                    <td class="fw-semibold text-dark">{{ $row->nama }}</td>
                    <td class="text-center text-muted small">
                        <span style="font-family: 'Consolas', monospace;">{{ $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-' }}</span>
                    </td>
                    <td class="text-center text-muted small">
                        <span style="font-family: 'Consolas', monospace;">{{ $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '-' }}</span>
                    </td>
                    <td class="text-center">
                        @include('pengeluaran.tombol', ['request' => $row])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">{{ __('No data available') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-4 pt-3 border-top px-3">
    {{ $pengeluaran->links('pagination::bootstrap-5') }}
</div>
