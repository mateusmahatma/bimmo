<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Bimmo')</title>

    <link rel="icon" id="favicon" href="{{ asset('img/bimmo_favicon.png') }}" type="image/png">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0984e3">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Bimmo">
    <link rel="apple-touch-icon" href="{{ asset('img/bimmo_favicon.png') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}?v={{ filemtime(public_path('css/sidebar.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/tombol.css') }}?v={{ filemtime(public_path('css/tombol.css')) }}" rel="stylesheet">
    @if(auth()->check() && auth()->user()->ui_style === 'milenial')
        <link href="{{ asset('css/millennial.css') }}?v={{ filemtime(public_path('css/millennial.css')) }}" rel="stylesheet">
    @endif


    @stack('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/rrule@2.6.4/dist/es5/rrule.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@6.1.10/index.global.min.js"></script>

    <script>
        (function() {
            window.applyTheme = (theme, persist = true) => {
                const isDark = theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');

                const favicon = document.getElementById('favicon');
                if (favicon) {
                    favicon.setAttribute('href', isDark ? "{{ asset('img/bimmo_dark_favicon.png') }}" : "{{ asset('img/bimmo_favicon.png') }}");
                }
                
                localStorage.setItem('theme', theme);

                if (persist) {
                    fetch("{{ route('user.update.skin') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ skin: theme })
                    }).then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    }).then(data => {
                        console.log('Theme synced successfully:', theme);
                    }).catch(err => {
                        console.error('Failed to sync theme to database:', err);
                    });
                }
            }

            // Initialization priority: 1. Server Skin 2. LocalStorage 3. Default Auto
            const serverSkin = "{{ auth()->user()->skin ?? '' }}";
            const localSkin = localStorage.getItem('theme');
            const initialTheme = serverSkin || localSkin || 'auto';
            
            applyTheme(initialTheme, false);

            // Monitor system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (localStorage.getItem('theme') === 'auto') {
                    applyTheme('auto', false);
                }
            });
        })();

        // Legacy/Shortcut function
        window.setTheme = (theme) => applyTheme(theme);

            // Robust PWA Detection
            const isPWAMode = window.matchMedia('(display-mode: standalone)').matches || 
                             window.navigator.standalone === true || 
                             document.referrer.includes('android-app://');

            if (isPWAMode) {
                document.documentElement.classList.add('pwa-mode');
            }

            // Disable common shortcuts ONLY for DevTools
            document.onkeydown = function(e) {
                if (
                    e.keyCode === 123 || // F12
                    (e.ctrlKey && e.shiftKey && [73, 74, 67].includes(e.keyCode)) // Ctrl+Shift+I/J/C
                ) {
                    return false;
                }
            };

            // Enhanced Console Protection - "Jangan tampilkan console nya"
            // Optimized to not impact performance
            const swallowConsole = () => {
                if (window.console) {
                    const methods = ['log', 'debug', 'info', 'warn', 'error', 'table', 'clear'];
                    methods.forEach(method => {
                        window.console[method] = () => {};
                    });
                }
            };
            
            // Execute console suppression
            swallowConsole();

            // Print Protection (Media query in CSS handles the rest)
            window.addEventListener('beforeprint', () => {
                document.body.style.display = 'none';
            });
            window.addEventListener('afterprint', () => {
                document.body.style.display = 'block';
            });

    </script>
</head>

<body class="{{ auth()->check() ? 'ui-style-' . (auth()->user()->ui_style ?? 'corporate') : '' }}" style="overflow-x: hidden;">
    @yield('body')

    @include('components.feedback-modal')
    @include('components.donate-modal')

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