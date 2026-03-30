@php
    $uiStyle = auth()->user()->ui_style ?? 'corporate';
@endphp
<table class="table {{ $uiStyle === 'milenial' ? 'table-borderless' : 'table-sm' }} align-middle mb-0">
    @if($uiStyle !== 'milenial')
    <thead class="table-light">
        <tr>
            <th>Month</th>
            <th class="text-end">Income</th>
            <th class="text-end">Expense</th>
            <th class="text-end">Difference</th>
        </tr>
    </thead>
    @endif
    <tbody>
        @forelse ($cashflow as $row)
        <tr class="{{ $uiStyle === 'milenial' ? 'm-list-item' : '' }}">
            <td class="{{ $uiStyle === 'milenial' ? 'ps-4 fw-bold' : '' }} border-0">{{ $row->bulan }}</td>
            <td class="text-end border-0">
                <span class="text-success fw-bold">Rp {{ number_format((float)$row->total_pemasukan, 0, ',', '.') }}</span>
            </td>
            <td class="text-end border-0">
                <span class="text-danger fw-bold">Rp {{ number_format((float)$row->total_pengeluaran, 0, ',', '.') }}</span>
            </td>
            <td class="text-end fw-bold border-0 {{ $uiStyle === 'milenial' ? 'pe-4' : '' }}">
                <div class="{{ $row->selisih >= 0 ? 'text-dark' : 'text-danger' }}">
                    Rp {{ number_format((float)$row->selisih, 0, ',', '.') }}
                    @if($row->selisih < 0)
                    <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-danger text-white ms-1">Defisit</span>
                    @elseif($row->selisih < 1000000)
                    <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-warning text-dark ms-1">Tipis</span>
                    @else
                    <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-success text-white ms-1">Aman</span>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center text-muted py-4">
                {{ __('No cash flow data available yet') }}
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
