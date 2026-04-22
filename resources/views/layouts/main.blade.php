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

        <main class="col-md-10 ms-sm-auto px-md-4 position-relative" style="min-height: 100vh; border-radius: 0 !important;">


            {{-- MOBILE TOP APP BAR --}}
            <div class="d-md-none py-2 mb-2 border-bottom d-flex align-items-center justify-content-center bg-white shadow-sm" style="position: sticky; top: 0; z-index: 1020; padding-top: max(8px, env(safe-area-inset-top)) !important;">
                <img src="{{ asset('img/bimmo_light.png') }}" class="sidebar-logo" alt="BIMMO" style="height: 25px;">
            </div>

            <div id="spa-container">
                @yield('container')
            </div>
        </main>
    </div>
</div>

{{-- BOTTOM NAVIGATION FOR MOBILE --}}
@include('layouts.bottom-nav')

<style>
    @media (max-width: 767.98px) {
        main {
            padding-bottom: 90px !important;
            /* Space for bottom nav */
        }
    }
</style>

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
                body: JSON.stringify({
                    language: lang
                })
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

<script src="{{ asset('js/ckeditor.js') }}"></script>
@endif
@endsection