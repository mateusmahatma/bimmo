@extends('layouts.main')

@section('title', __('Net Worth Detail'))

@push('css')
<link href="{{ asset('css/dashboard/net-worth.css') }}?v={{ filemtime(public_path('css/dashboard/net-worth.css')) }}" rel="stylesheet">
@endpush

@section('container')
@include('dashboard.partials.net-worth-page-content')
@endsection

@push('scripts')
<script>
    window.netWorthPageConfig = {
        periode: {{ $periode }},
        historyUrl: "{{ route('dashboard.net-worth-history') }}",
        labels: {
            syncing: @json(__('Syncing...')),
            sync: @json(__('Sync')),
            netWorth: @json(__('Net Worth')),
            wealth: @json(__('Wealth')),
            debt: @json(__('Debt')),
            wealthDetails: @json(__('Wealth Details')),
            debtDetails: @json(__('Debt Details')),
            asset: @json(__('Asset')),
            emergencyFund: @json(__('Emergency Fund')),
            wallet: @json(__('Wallet')),
            loan: @json(__('Loan')),
            name: @json(__('Name')),
            category: @json(__('Category')),
            amount: @json(__('Amount')),
            noRecords: @json(__('No records found for this month.')),
            fetchError: @json(__('Gagal memproses data. Silakan coba lagi.')),
        },
    };
</script>
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('js/dashboard-net-worth-page.js') }}?v={{ filemtime(public_path('js/dashboard-net-worth-page.js')) }}"></script>
@endpush
