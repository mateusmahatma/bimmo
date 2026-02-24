@extends('layouts.app')

@section('body')
<div class="container-fluid py-3">
    <div class="row">
        {{-- DESKTOP SIDEBAR --}}
        <aside class="col-md-2 bg-light border-end d-none d-md-block">
            @include('layouts.sidebar', ['prefix' => 'desktop_'])
        </aside>

        {{-- MOBILE OFFCANVAS SIDEBAR --}}
        <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                @include('layouts.sidebar', ['prefix' => 'mobile_'])
            </div>
        </div>

        <main class="col-md-10 ms-sm-auto px-md-4">
            {{-- MOBILE TOGGLE BUTTON --}}
            <div class="d-md-none py-2 mb-3 border-bottom d-flex align-items-center">
                <button class="btn btn-outline-secondary btn-sm me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <img src="{{ asset('img/bimmo_2.png') }}" alt="BIMMO" style="height: 25px;">
            </div>

            @yield('container')
        </main>
    </div>
</div>

{{-- Daily Transaction Notification Toast --}}
@if(auth()->check() && auth()->user()->daily_notification)
    @php
        $hasTransactionToday = \App\Models\Transaksi::where('id_user', auth()->id())
            ->whereDate('tgl_transaksi', \Carbon\Carbon::today())
            ->exists();
    @endphp

    @if(!$hasTransactionToday)
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="transactionToast" class="toast hide shadow-lg border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header bg-warning text-dark border-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong class="me-auto">Reminder Transaksi</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white rounded-bottom">
                <p class="mb-2">Anda belum mencatat transaksi apapun hari ini! Mari catat sekarang agar keuangan tetap terpantau.</p>
                <div class="d-grid">
                    <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        #transactionToast {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            border-radius: 12px;
            overflow: hidden;
        }
        #transactionToast .toast-header {
            background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%) !important;
            padding: 12px 16px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastEl = document.getElementById('transactionToast');
            if (!toastEl) return;
            
            const toast = new bootstrap.Toast(toastEl);
            const interval = {{ auth()->user()->notification_interval ?? 30 }} * 60 * 1000; // minutes to ms
            
            function shouldShowNotification() {
                const lastDismissed = localStorage.getItem('transaction_notification_dismissed');
                if (!lastDismissed) return true;
                
                const now = new Date().getTime();
                return (now - parseInt(lastDismissed)) >= interval;
            }

            function showNotification() {
                if (shouldShowNotification()) {
                    toast.show();
                }
            }

            // Initial check
            showNotification();

            // Recurring check
            setInterval(showNotification, 60000); // Check every minute

            toastEl.addEventListener('hidden.bs.toast', function () {
                localStorage.setItem('transaction_notification_dismissed', new Date().getTime());
            });
        });
    </script>
    @endif
@endif

@endsection