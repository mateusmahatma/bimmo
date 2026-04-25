@extends('layouts.main')

@section('title', 'Dashboard')

@section('container')

@include('dashboard.partials.page-header')

<section class="dashboard">
    <div class="row g-4">

        {{-- KOLOM KIRI --}}
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">
            @include('dashboard.partials.financial-summary')
            @include('dashboard.partials.cash-flow')
            @include('dashboard.partials.net-worth')
            @include('dashboard.partials.financial-goals')

        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">
            @include('dashboard.partials.emergency-fund')
            @include('dashboard.partials.debt-ratio')
            @include('dashboard.partials.expense-bar')

        </div>

        {{-- CALENDAR (full width) --}}
        <div class="col-12">
            @include('dashboard.partials.calendar')
        </div>

        {{-- TODAY'S TRANSACTIONS (full width) --}}
        <div class="col-12">
            @include('dashboard.partials.today-transactions')
        </div>

        {{-- BUDGET PERFORMANCE (full width) --}}
        <div class="col-12">
            @include('dashboard.partials.budget-performance')
        </div>

        @include('dashboard.partials.modals')

    </div>
</section>

@endsection

@push('scripts')
<script>
    window.cashflowData       = @json($cashflow ?? []);
    window.dashboardFilterUrl = "{{ route('dashboard.filter') }}";
    window.eventsUrl          = "{{ url('events') }}";
    window.netWorthData       = null;
</script>
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('js/dashboard-cashflow.js') }}?v={{ filemtime(public_path('js/dashboard-cashflow.js')) }}"></script>
<script src="{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}"></script>
<script src="{{ asset('js/calendar.js') }}?v={{ filemtime(public_path('js/calendar.js')) }}"></script>
@endpush
