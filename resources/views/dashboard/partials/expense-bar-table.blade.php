@php
    $uiStyle = auth()->user()->ui_style ?? 'corporate';
@endphp
<div class="table-responsive">
    <table class="table {{ $uiStyle === 'milenial' ? 'table-borderless' : 'table-sm' }} mb-0">
        @if($uiStyle !== 'milenial')
        <thead class="table-light">
            <tr>
                <th>Category</th>
                <th class="text-end">Total</th>
                <th class="text-end">%</th>
            </tr>
        </thead>
        @endif
        <tbody>
            @forelse ($pengeluaranKategori as $row)
            <tr class="{{ $uiStyle === 'milenial' ? 'm-list-item' : '' }}">
                <td class="{{ $uiStyle === 'milenial' ? 'ps-4 fw-bold' : '' }} border-0">{{ $row->kategori }}</td>
                <td class="text-end border-0">
                    <span class="fw-semibold text-dark">Rp {{ number_format((float)$row->total,0,',','.') }}</span>
                </td>
                <td class="text-end fw-bold border-0 {{ $uiStyle === 'milenial' ? 'pe-4' : '' }}
                    {{ $row->persen > 40 ? 'text-danger' : ($row->persen > 25 ? 'text-warning' : 'text-success') }}">
                    {{ $row->persen }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    {{ __('No expense data available') }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
