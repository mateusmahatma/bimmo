<table class="table table-sm mb-0">
    <thead class="table-light">
        <tr>
            <th>Month</th>
            <th class="text-end">Saving Rate (%)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($savingRate as $row)
        <tr>
            <td>{{ \Carbon\Carbon::parse($row->bulan.'-01')->translatedFormat('F Y') }}</td>
            <td class="text-end fw-bold
                {{ $row->saving_rate >= 0 ? 'text-success' : 'text-danger' }}">
                <span class="badge bg-{{ $row->saving_class }}">
                    {{ $row->saving_rate }}% – {{ $row->saving_label }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="small text-muted mt-2">
    <strong>Explanation:</strong>
    <span class="badge bg-success">Very Healthy</span>
    <span class="badge bg-primary">Healthy</span>
    <span class="badge bg-warning text-dark">Warning</span>
    <span class="badge bg-danger">Deficit</span>
</div>
