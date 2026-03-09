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
            @if(auth()->check())
                {{-- Subscription Banner Data --}}
                @php
                    $user = auth()->user();
                    $remainingDays = $user->getRemainingDays();
                    $isSubscribed = $user->isSubscribed();
                    $isOnTrial = $user->isOnTrial();
                    $canAccess = $user->canAccessFeatures();
                @endphp

                @if($canAccess && $remainingDays <= 3)
                    <div class="alert alert-warning fade show rounded-3 mb-3 border-0 text-center py-2 sticky-top mt-2" role="alert" style="background: #fff3cd; color: #856404; font-size: 0.9rem; z-index: 1020; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <i class="bi bi-clock-history me-2"></i>
                        @if($remainingDays == 0)
                            {{ __('Your subscription/trial expires') }} <strong>{{ __('Today') }}</strong>.
                        @else
                            {{ __('Your subscription/trial expires in') }} <strong>{{ $remainingDays }} {{ __('days') }}</strong>.
                        @endif
                        <a href="{{ route('profil.index') }}" class="alert-link text-decoration-underline ms-2">{{ __('Renew now') }}</a> to maintain access.
                    </div>
                @endif
            @endif
            {{-- MOBILE TOGGLE BUTTON --}}
            <div class="d-md-none py-2 mb-3 border-bottom d-flex align-items-center">
                <button class="btn btn-outline-secondary btn-sm me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <img src="{{ asset('img/bimmo_light.png') }}" alt="BIMMO" style="height: 25px;">
            </div>

            @yield('container')
        </main>
    </div>
</div>

@if(auth()->check())
    {{-- Restrict Access Overlay/Modal logic --}}
    @if(!$canAccess && !request()->routeIs('profil.index'))
        <div id="restrictionOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7); z-index: 2000;">
            <div class="card border-0 shadow-lg text-center p-4" style="max-width: 400px; border-radius: 20px;">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="bg-danger-light text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; background: rgba(220, 53, 69, 0.1);">
                            <i class="bi bi-lock-fill fs-1"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-3">{{ __('Access Expired') }}</h3>
                    <p class="text-muted mb-4">{{ __("Your subscription or trial period has ended. Please subscribe to continue using BIMMO's features.") }}</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('profil.index') }}" class="btn btn-primary btn-lg rounded-pill">
                            <i class="bi bi-person-fill me-2"></i> {{ __('Go to Profile to Subscribe') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <style>
            body { overflow: hidden !important; }
        </style>
    @endif
@endif

{{-- Daily Transaction Notification Toast --}}
@if(auth()->check() && auth()->user()->daily_notification)
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="transactionToast" class="toast hide shadow-lg border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header bg-warning text-dark border-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong class="me-auto">{{ __('Reminder Transaksi') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white rounded-bottom">
                <p class="mb-2">{{ __("Anda belum mencatat transaksi apapun hari ini! Mari catat sekarang agar keuangan tetap terpantau.") }}</p>
                <div class="d-grid">
                    <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> {{ __('Tambah Transaksi') }}
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

            async function checkAndShowNotification() {
                try {
                    const response = await fetch("{{ url('dashboard/todayTransactions') }}", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const transactions = await response.json();
                    
                    if (transactions.length === 0) {
                        if (shouldShowNotification()) {
                            // Show Bootstrap Toast (In-app)
                            toast.show();

                            // Show Browser Native Notification (OS-level)
                            if ("Notification" in window) {
                                if (Notification.permission === "granted") {
                                    sendNativeNotification();
                                } else if (Notification.permission !== "denied") {
                                    Notification.requestPermission().then(permission => {
                                        if (permission === "granted") {
                                            sendNativeNotification();
                                        }
                                    });
                                }
                            }
                        }
                    } else {
                        // If there are transactions, hide the toast if it's currently showing
                        toast.hide();
                    }
                } catch (error) {
                    console.error('Error checking transactions:', error);
                }
            }

            function sendNativeNotification() {
                const options = {
                    body: "Anda belum mencatat transaksi apapun hari ini! Mari catat sekarang agar keuangan tetap terpantau.",
                    icon: "{{ asset('img/bimmo_2.png') }}",
                    badge: "{{ asset('img/bimmo_2.png') }}",
                    tag: "daily-transaction-reminder",
                    requireInteraction: true
                };
                
                const n = new Notification("Reminder Transaksi - BIMMO", options);
                
                n.onclick = function(event) {
                    event.preventDefault(); 
                    window.open("{{ route('transaksi.create') }}", "_blank");
                    n.close();
                };
            }

            // Initial check
            checkAndShowNotification();

            // Recurring check
            setInterval(checkAndShowNotification, 60000); // Check every minute

            toastEl.addEventListener('hidden.bs.toast', function () {
                localStorage.setItem('transaction_notification_dismissed', new Date().getTime());
            });
        });
    </script>
@endif

    <script>
        function updateLanguage(lang) {
            fetch("{{ route('user.update.language') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ language: lang })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        location.reload();
                    }, 300);
                } else {
                    alert('Gagal mengubah bahasa.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan.');
            });
        }
    </script>
@endsection