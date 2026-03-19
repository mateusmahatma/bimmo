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

        <main class="col-md-10 ms-sm-auto px-md-4 position-relative" style="min-height: 100vh;">
            <!-- Loading Overlay -->
            <div id="page-loader-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-none justify-content-center pt-5" style="background: rgba(255, 255, 255, 0.6); z-index: 9999; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); transition: opacity 0.3s ease; opacity: 0;">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; position: sticky; top: 30vh;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
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
                <img src="{{ asset('img/bimmo_light.png') }}" class="sidebar-logo" alt="BIMMO" style="height: 25px;">
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

@if(auth()->check())
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
    <!-- Page Transition Loader Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loader = document.getElementById('page-loader-overlay');
            if (!loader) return;
            
            // Adjust loader overlay background based on theme
            const adjustLoaderTheme = () => {
                if(document.documentElement.getAttribute('data-bs-theme') === 'dark') {
                    loader.style.background = 'rgba(33, 37, 41, 0.6)';
                } else {
                    loader.style.background = 'rgba(255, 255, 255, 0.6)';
                }
            };
            
            adjustLoaderTheme();
            
            // Observe theme changes
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'data-bs-theme') {
                        adjustLoaderTheme();
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
            
            document.body.addEventListener('click', function(e) {
                if (e.defaultPrevented) return;

                const link = e.target.closest('a');
                if (!link) return;
                
                const href = link.getAttribute('href');
                const target = link.getAttribute('target');
                
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.includes('mailto:') || href.includes('tel:') || target === '_blank' || link.hasAttribute('data-bs-toggle') || link.classList.contains('no-loader')) {
                    return;
                }
                
                try {
                    const url = new URL(href, window.location.origin);
                    if (url.origin === window.location.origin) {
                        // Ignore pure hash changes
                        if (url.pathname === window.location.pathname && url.search === window.location.search) {
                            return; 
                        }
                        // Ignore modifier keys
                        if (e.ctrlKey || e.shiftKey || e.metaKey || e.which === 2) {
                            return;
                        }
                        // Show Loader
                        loader.classList.add('d-flex');
                        loader.classList.remove('d-none');
                        void loader.offsetWidth; // Trigger reflow
                        loader.style.opacity = '1';
                    }
                } catch (err) {}
            });

            document.body.addEventListener('submit', function(e) {
                if (e.defaultPrevented) return;
                
                const form = e.target;
                if (form.getAttribute('target') !== '_blank' && !form.classList.contains('no-loader')) {
                    loader.classList.add('d-flex');
                    loader.classList.remove('d-none');
                    void loader.offsetWidth;
                    loader.style.opacity = '1';
                }
            });

            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.classList.add('d-none');
                        loader.classList.remove('d-flex');
                    }, 300);
                }
            });
        });
    </script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
@endif
@endsection