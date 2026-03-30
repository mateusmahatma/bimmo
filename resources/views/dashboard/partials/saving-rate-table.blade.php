@php
    $uiStyle = auth()->user()->ui_style ?? 'corporate';
@endphp
<table class="table {{ $uiStyle === 'milenial' ? 'table-borderless' : 'table-sm' }} mb-0">
    @if($uiStyle !== 'milenial')
    <thead class="table-light">
        <tr>
            <th>Month</th>
            <th class="text-end">Saving Rate (%)</th>
        </tr>
    </thead>
    @endif
    <tbody>
        @foreach ($savingRate as $row)
        <tr class="{{ $uiStyle === 'milenial' ? 'm-list-item' : '' }}">
            <td class="{{ $uiStyle === 'milenial' ? 'ps-4 fw-bold' : '' }} border-0">{{ \Carbon\Carbon::parse($row->bulan.'-01')->translatedFormat('F Y') }}</td>
            <td class="text-end fw-bold border-0 {{ $uiStyle === 'milenial' ? 'pe-4' : '' }}
                {{ $row->saving_rate >= 0 ? 'text-success' : 'text-danger' }}">
                <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-{{ $row->saving_class }}">
                    {{ $row->saving_rate }}% – {{ $row->saving_label }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="small text-muted mt-3 {{ $uiStyle === 'milenial' ? 'px-4' : '' }}">
    <strong>Explanation:</strong>
    <div class="mt-2 d-flex flex-wrap gap-2">
        <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-success text-white">Very Healthy</span>
        <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-primary text-white">Healthy</span>
        <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-warning text-dark">Warning</span>
        <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-danger text-white">Deficit</span>
    </div>
</div>
