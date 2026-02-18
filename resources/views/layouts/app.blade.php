<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','BIMMO')</title>

    <link rel="icon" href="{{ asset('img/bimmo_favicon.png') }}" type="image/x-icon">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tombol.css') }}?v={{ filemtime(public_path('css/tombol.css')) }}" rel="stylesheet">


    @stack('css')
    
    <script>
        (function() {
            const setTheme = (theme) => {
                if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', theme);
                }
            }

            // Check system preference on load
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
            setTheme(prefersDark.matches ? 'dark' : 'light');

            // Monitor system changes
            prefersDark.addEventListener('change', event => {
                setTheme(event.matches ? 'dark' : 'light');
            });
        })();
    </script>
</head>

<body>

    @yield('body')

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @stack('scripts')

    <!-- Session Timeout Modal -->
    <div class="modal fade" id="sessionTimeoutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sessionTimeoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sessionTimeoutModalLabel">Session Expired</h5>
                </div>
                <div class="modal-body">
                    We apologize, your session has expired. Please log in again.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnSessionExpired">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sessionLifetime = {{ config('session.lifetime') }} * 60 * 1000; // minutes to milliseconds
            let timeoutTimer;
            const modalElement = document.getElementById('sessionTimeoutModal');
            let sessionModal;
            
            if (modalElement) {
                sessionModal = new bootstrap.Modal(modalElement);
                
                document.getElementById('btnSessionExpired').addEventListener('click', function() {
                    window.location.href = "{{ route('login') }}";
                });
            }

            function startTimer() {
                clearTimeout(timeoutTimer);
                if (sessionLifetime > 0) {
                    timeoutTimer = setTimeout(function() {
                        showSessionExpired();
                    }, sessionLifetime);
                }
            }

            function showSessionExpired() {
                if (sessionModal) {
                    sessionModal.show();
                }
            }

            // Start the timer initially
            startTimer();

            // Intercept fetch requests to monitor for 401/419 and reset timer on success
            const originalFetch = window.fetch;
            window.fetch = function() {
                return originalFetch.apply(this, arguments)
                    .then(response => {
                        if (response.status === 401 || response.status === 419) {
                            showSessionExpired();
                        } else if (response.ok) {
                            // Reset timer on successful interaction
                            startTimer();
                        }
                        return response;
                    })
                    .catch(error => {
                        // Network error or other issues, typically don't reset timer here unless we are sure content was loaded
                        throw error;
                    });
            };
        });
    </script>
</body>

</html>