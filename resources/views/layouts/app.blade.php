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


    @stack('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/rrule@2.6.4/dist/es5/rrule.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@6.1.10/index.global.min.js"></script>

    <script>
        (function() {
            const setTheme = (theme) => {
                const isDark = theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');

                // Dynamic Favicon Switching
                const favicon = document.getElementById('favicon');
                if (favicon) {
                    favicon.setAttribute('href', isDark ? "{{ asset('img/bimmo_dark_favicon.png') }}" : "{{ asset('img/bimmo_favicon.png') }}");
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

        // Extreme Source Code Protection
        (function() {
            // Robust PWA Detection
            const isPWAMode = window.matchMedia('(display-mode: standalone)').matches || 
                             window.navigator.standalone === true || 
                             document.referrer.includes('android-app://');

            if (isPWAMode) {
                console.log('PWA mode detected. Protection system disabled for native experience.');
                document.documentElement.classList.add('pwa-mode');
                return; // Bypass all protections
            }

            // Disable right-click
            document.addEventListener('contextmenu', e => e.preventDefault());

            // Disable common shortcuts
            document.onkeydown = function(e) {
                if (
                    e.keyCode === 123 || // F12
                    (e.ctrlKey && e.shiftKey && [73, 74, 67].includes(e.keyCode)) || // Ctrl+Shift+I/J/C
                    (e.ctrlKey && e.keyCode === 85) || // Ctrl+U
                    (e.ctrlKey && e.keyCode === 83) // Ctrl+S
                ) {
                    return false;
                }
            };

            const isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const isMobileOrPWA = isMobileDevice || isPWAMode;

            const blockAccess = () => {
                if (isMobileOrPWA) return; // Skip threshold-based blocking on mobile/PWA
                document.body.innerHTML = `
                    <div style="display:flex;justify-content:center;align-items:center;height:100vh;flex-direction:column;font-family:sans-serif;background:#f8f9fa;">
                        <h1 style="color:#dc3545;">Akses Dibatasi</h1>
                        <p style="color:#6c757d;">Developer Tools terdeteksi. Silakan tutup Developer Tools untuk melanjutkan.</p>
                        <button onclick="window.location.reload()" style="padding:10px 20px;border:none;background:#007bff;color:white;border-radius:5px;cursor:pointer;">Muat Ulang Halaman</button>
                    </div>
                `;
                document.body.style.overflow = 'hidden';
            };

            // Detect DevTools by monitoring window dimensions
            let threshold = 160;
            const checkDevTools = () => {
                const widthDiff = window.outerWidth - window.innerWidth > threshold;
                const heightDiff = window.outerHeight - window.innerHeight > threshold;

                if (widthDiff || heightDiff) {
                    blockAccess();
                }
            };
            window.addEventListener('resize', checkDevTools);
            checkDevTools();

            // Detect DevTools using a specialized getter
            const devtools = {
                isOpen: false,
                orientation: undefined
            };
            const threshold2 = 160;
            const emitEvent = (isOpen, orientation) => {
                if (isOpen) {
                    blockAccess();
                }
            };

            setInterval(() => {
                const widthThreshold = window.outerWidth - window.innerWidth > threshold2;
                const heightThreshold = window.outerHeight - window.innerHeight > threshold2;
                const orientation = widthThreshold ? 'vertical' : 'horizontal';

                if (!(heightThreshold && widthThreshold) &&
                    ((window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized) || widthThreshold || heightThreshold)) {
                    if (!devtools.isOpen || devtools.orientation !== orientation) {
                        emitEvent(true, orientation);
                    }
                    devtools.isOpen = true;
                    devtools.orientation = orientation;
                } else {
                    if (devtools.isOpen) {
                        emitEvent(false, undefined);
                    }
                    devtools.isOpen = false;
                    devtools.orientation = undefined;
                }
            }, 500);

            // Robust Debugger Trap via Function constructor
            const detector = function() {
                try {
                    (function() {
                        (function a() {
                            try {
                                (function b(i) {
                                    if (('' + (i / i)).length !== 1 || i % 20 === 0) {
                                        (function() {}).constructor('debugger')();
                                    } else {
                                        debugger;
                                    }
                                    b(++i);
                                })(0);
                            } catch (e) {
                                setTimeout(a, 50);
                            }
                        })();
                    })();
                } catch (e) {}
            };

            // Start the extreme debugger trap
            setTimeout(detector, 1000);

            // Continuous console clearing
            setInterval(() => console.clear(), 100);

            // Hide content on tab switch or minimize
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    document.body.style.filter = 'blur(20px)';
                    document.title = 'Bimmo - Protected';
                } else {
                    document.body.style.filter = 'none';
                    document.title = '{{ $title ?? "Bimmo" }}';
                }
            });

            let isLocked = false;

            let protectionTimeout;

            // Netflix-style Black Out Protection
            const hideContent = (isInstant = false) => {
                const triggerHide = () => {
                    document.body.classList.add('protection-active');
                    const overlay = document.getElementById('protection-overlay');
                    if (overlay) overlay.style.display = 'flex';
                };

                if (isInstant || !isMobileOrPWA) {
                    triggerHide();
                } else {
                    // 1s Grace period for mobile/PWA to avoid false positives during system gestures
                    clearTimeout(protectionTimeout);
                    protectionTimeout = setTimeout(triggerHide, 1000);
                }
            };

            const showContent = () => {
                clearTimeout(protectionTimeout); // Cancel pending hide
                if (isLocked) return; // Never show content again if locked by capture
                document.body.classList.remove('protection-active');
                const overlay = document.getElementById('protection-overlay');
                if (overlay) overlay.style.display = 'none';
            };

            window.addEventListener('blur', hideContent);
            window.addEventListener('focus', showContent);

            // Extreme Focus Sentry - Frame Perfect (requestAnimationFrame)
            let isPageActive = false;
            let lastFocusLoss = 0;

            const checkFocus = () => {
                if (!isPageActive) {
                    requestAnimationFrame(checkFocus);
                    return;
                }

                if (!document.hasFocus()) {
                    hideContent();
                    lastFocusLoss = Date.now();
                } else {
                    // Hysteretic recovery: Stay hidden for at least 500ms after focus loss
                    if (Date.now() - lastFocusLoss > 500) {
                        showContent();
                    }
                }
                requestAnimationFrame(checkFocus);
            };

            // Only start the aggressive sentry after initial load and first user interaction
            window.addEventListener('load', () => {
                setTimeout(() => {
                    isPageActive = true;
                    requestAnimationFrame(checkFocus);
                }, 1000); // 1s grace period for reloads
            });

            // Re-verify activity on user interaction to ensure sentry is running
            ['touchstart', 'mousedown', 'keydown', 'scroll'].forEach(evt => {
                window.addEventListener(evt, () => {
                    if (!isPageActive) isPageActive = true;
                }, {
                    passive: true
                });
            });

            // Mobile Multi-finger Touch Detection (Common screenshot gestures)
            document.addEventListener('touchstart', (e) => {
                if (e.touches.length > 2) { // 3 or more fingers
                    hideContent(true); // Instant hide for multi-finger gestures
                    clearClipboard();
                    isLocked = true;
                }
            }, {
                passive: true
            });

            // Aggressive Protection Logic
            const clearClipboard = () => {
                try {
                    const dummy = document.createElement("input");
                    dummy.style.position = 'fixed';
                    dummy.style.opacity = '0';
                    document.body.appendChild(dummy);
                    dummy.value = "KONTEN DILINDUNGI - Bimmo - " + new Date().getTime();
                    dummy.select();
                    document.execCommand("copy");
                    document.body.removeChild(dummy);
                } catch (err) {}
            };

            // Preemptive Blurring for Modifier Combinations (Win+Shift, Ctrl+Shift, Cmd+Shift)
            document.addEventListener('keydown', (e) => {
                // If user starts pressing modifier combinations (often used for screenshots like Win+Shift+S or Cmd+Shift+4)
                const isModCombo = (e.ctrlKey && e.shiftKey) || (e.metaKey && e.shiftKey);
                
                if (isModCombo) {
                    hideContent();
                }

                // F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S, etc.
                if (
                    e.keyCode === 123 ||
                    (e.ctrlKey && e.shiftKey && [73, 74, 67].includes(e.keyCode)) ||
                    (e.ctrlKey && [85, 83, 80].includes(e.keyCode)) || // U, S, P (Print)
                    e.key === 'PrintScreen' || e.keyCode === 44
                ) {
                    isLocked = true; // Permanent lock
                    hideContent(true); // Instant hide for shortcuts
                    clearClipboard();

                    if (e.key === 'PrintScreen' || e.keyCode === 44) {
                        e.preventDefault();
                    }
                }
            });

            document.addEventListener('keyup', (e) => {
                // Show content again if only modifier keys were pressed and no capture was triggered
                if (['Meta', 'Shift', 'Control', 'Alt'].includes(e.key)) {
                    setTimeout(() => {
                        if (!isLocked && document.hasFocus()) {
                            showContent();
                        }
                    }, 500);
                }

                if (e.key === 'PrintScreen' || e.keyCode === 44) {
                    isLocked = true;
                    hideContent();
                }
            });

            // Prevent common capture shortcuts (meta/cmd keys)
            window.addEventListener('keyup', (e) => {
                if (e.key === 'Meta' || e.key === 'OS' || e.key === 'Windows') {
                    // Possible Win+Shift+S attempt
                    clearClipboard();
                }
            });

            // Secondary Print Protection
            window.addEventListener('beforeprint', () => {
                document.body.style.display = 'none';
            });
            window.addEventListener('afterprint', () => {
                document.body.style.display = 'block';
            });
        })();
    </script>
</head>

<body>
    <div id="protection-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:#000;justify-content:center;align-items:center;flex-direction:column;font-family:sans-serif;padding: 20px; text-align: center;">
        <div style="width: 80px; height: 80px; background: #1a1a1a; color: #dc3545; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 1.5rem;">
            <i class="bi bi-camera-fill"></i>
        </div>
        <h2 style="color:#fff; font-weight: 700; margin-bottom: 1rem;">Akses Dibatasi</h2>
        <p style="color:#aaa; max-width: 400px; line-height: 1.6; margin-bottom: 2rem;">
            Sistem mendeteksi upaya pengambilan gambar (screenshot/capture). Untuk melindungi data sensitif Anda, akses ke halaman ini telah dikunci.
        </p>
        <button onclick="window.location.reload()" style="background: #0984e3; color: white; border: none; padding: 0.8rem 2rem; border-radius: 10px; font-weight: 600; cursor: pointer;">
            Muat Ulang Halaman
        </button>
    </div>

    @yield('body')

    @include('components.feedback-modal')

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
            const sessionLifetime = {
                {
                    config('session.lifetime')
                }
            }* 60 * 1000; // minutes to milliseconds
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
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .then(reg => console.log('Service Worker registered', reg))
                    .catch(err => console.log('Service Worker not registered', err));
            });
        }
    </script>
</body>

</html>