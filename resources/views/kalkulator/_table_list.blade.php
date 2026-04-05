<div class="table-responsive">
    @php
        $currentSort = request('sort', 'created_at');
        $currentDir = request('direction', 'desc');

        $sortLink = function ($column) {
            $dir = request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
        };
    @endphp
    <table id="hasilAnggaranTable" class="table table-hover align-middle mb-0" style="width:100%">
        <thead class="bg-light">
            <tr style="border-bottom: 2px solid #edf2f9;">
                <th style="width: 5%;" class="text-center py-3">
                    <div class="form-check d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" id="checkAll" style="cursor: pointer;">
                    </div>
                </th>
                <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('No') }}</th>
                <th class="text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('tanggal_mulai') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1 sort-link" data-sort="tanggal_mulai" data-direction="{{ $currentSort === 'tanggal_mulai' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('Period') }} @if ($currentSort === 'tanggal_mulai') <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </a>
                </th>
                <th class="text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('nama_anggaran') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1 sort-link" data-sort="nama_anggaran" data-direction="{{ $currentSort === 'nama_anggaran' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('Budget Name') }} @if ($currentSort === 'nama_anggaran') <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </a>
                </th>
                <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Expense Type') }}</th>
                <th class="text-center text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('persentase_anggaran') }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1 sort-link" data-sort="persentase_anggaran" data-direction="{{ $currentSort === 'persentase_anggaran' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('Pct') }} @if ($currentSort === 'persentase_anggaran') <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </a>
                </th>
                <th class="text-end text-secondary small text-uppercase fw-bold py-3">
                    <a href="{{ $sortLink('nominal_anggaran') }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-end gap-1 sort-link" data-sort="nominal_anggaran" data-direction="{{ $currentSort === 'nominal_anggaran' && $currentDir === 'asc' ? 'desc' : 'asc' }}">
                        {{ __('Budget') }} @if ($currentSort === 'nominal_anggaran') <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </a>
                </th>
                <th class="text-end text-secondary small text-uppercase fw-bold py-3">{{ __('Used') }}</th>
                <th class="text-end text-secondary small text-uppercase fw-bold py-3">{{ __('Remaining') }}</th>
                <th style="width: 5%;" class="text-center text-secondary small text-uppercase fw-bold py-3">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hasilProses as $row)
                <tr>
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input check-item" type="checkbox" value="{{ $row->hash }}" style="cursor: pointer;">
                        </div>
                    </td>
                    <td class="text-center text-secondary fw-medium" data-label="{{ __('No') }}">{{ $loop->iteration + ($hasilProses->currentPage() - 1) * $hasilProses->perPage() }}</td>
                    <td data-label="{{ __('Period') }}">
                        <span class="fw-medium text-dark">{{ \Carbon\Carbon::parse($row->tanggal_mulai)->format('d M Y') }}</span><br>
                        <span class="text-muted small">sampai {{ \Carbon\Carbon::parse($row->tanggal_selesai)->format('d M Y') }}</span>
                    </td>
                    <td class="fw-bold text-dark" data-label="{{ __('Budget Name') }}">{{ $row->nama_anggaran }}</td>
                    <td data-label="{{ __('Expense Type') }}">
                        @php
                            $names = explode(', ', $row->nama_jenis_pengeluaran);
                            $limit = 3;
                        @endphp
                        <ul class="list-unstyled mb-0 small text-muted">
                            @foreach(array_slice($names, 0, $limit) as $name)
                                <li><i class="bi bi-dot"></i> {{ $name }}</li>
                            @endforeach
                            @if(count($names) > $limit)
                                <li class="ms-3">+{{ count($names) - $limit }} lainnya</li>
                            @endif
                        </ul>
                    </td>
                    <td class="text-center" data-label="{{ __('Pct') }}">
                        <span class="badge bg-light text-dark border-0 shadow-none" style="font-size: 0.75rem; padding: 4px 8px;">{{ number_format($row->persentase_anggaran, 0) }}%</span>
                    </td>
                    <td class="text-end fw-semibold text-dark" data-label="{{ __('Budget') }}">
                        Rp {{ number_format($row->nominal_anggaran, 0, ',', '.') }}
                    </td>
                    <td class="text-end text-danger fw-medium" data-label="{{ __('Used') }}">
                        Rp {{ number_format($row->anggaran_yang_digunakan, 0, ',', '.') }}
                    </td>
                    <td class="text-end" data-label="{{ __('Remaining') }}">
                        @php $sisa = $row->nominal_anggaran - $row->anggaran_yang_digunakan; @endphp
                        <span class="fw-bold {{ $sisa < 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </span><br>
                        @if($sisa < 0)
                            <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">{{ __('Over Budget') }}</span>
                        @else
                            <span class="badge bg-success-subtle text-success" style="font-size: 10px;">{{ __('Within Budget') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill btn-sync-anggaran" data-id="{{ $row->hash }}" style="padding: 2px 8px; font-size: 0.7rem;" title="{{ __('Sync Data') }}">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            <a href="{{ route('kalkulator.show', $row->hash) }}" class="btn btn-outline-primary btn-sm rounded-pill" style="padding: 2px 8px; font-size: 0.7rem;" title="{{ __('Detail') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill tombol-del-proses-anggaran" data-id="{{ $row->hash }}" style="padding: 2px 8px; font-size: 0.7rem;" title="{{ __('Delete') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">{{ __('No budget history found.') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-4 pt-3 border-top px-3">
    {{ $hasilProses->links('pagination::bootstrap-5') }}
</div>
