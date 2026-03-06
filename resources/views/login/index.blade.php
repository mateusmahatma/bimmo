<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Bimmo</title>
    <link rel="icon" id="favicon" href="{{ asset('img/bimmo_favicon.png') }}" type="image/png">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0984e3">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Bimmo">
    <link rel="apple-touch-icon" href="{{ asset('img/bimmo_favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('css/style_login.css') }}?v={{ filemtime(public_path('css/style_login.css')) }}" rel="stylesheet" />
    <link href="/css/all.min.css" rel="stylesheet" />
    <script>
        // PWA Diagnostic Suite
        window.deferredPrompt = null;
        console.log('PWA Debug: Starting diagnostics...');

        // 1. Listen for the install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('PWA Debug: EVENT beforeinstallprompt fired!');
            e.preventDefault();
            window.deferredPrompt = e;
            const btn = document.getElementById('installPwa');
            if (btn) btn.style.display = 'block';
        });

        // 2. Check current mode
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
            console.log('PWA Debug: STATUS Running in Standalone (App) mode');
        }

        // 3. Verify Manifest and Icons after load
        window.addEventListener('load', () => {
            const manifestLink = document.querySelector('link[rel="manifest"]');
            if (!manifestLink) {
                console.error('PWA Debug: ERROR Manifest link missing in head!');
            } else {
                console.log('PWA Debug: Manifest link found:', manifestLink.href);
                fetch(manifestLink.href).then(r => console.log('PWA Debug: Manifest file is reachable')).catch(e => console.error('PWA Debug: Manifest file NOT reachable!', e));
            }

            const img = new Image();
            img.onload = () => console.log('PWA Debug: Icon file is reachable');
            img.onerror = () => console.error('PWA Debug: Icon file NOT reachable!');
            img.src = '/img/pwa-icon-512.png';
        });
    </script>
</head>

<body>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="card-header">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <div class="text-center">
                                            <img src="/img/bimmo.png" alt="" class="mb-3" style="max-width: 120px;">
                                        </div>
                                        <h5 class="card-title text-center pb-0 fs-4">Welcome back</h5>
                                        <p class="text-center small">Enter your username and password to log in.</p>
                                    </div>
                                    @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                    @endif
                                    <form class="row g-3 needs-validation" action="/login" method="post">
                                        @csrf
                                        <div class="form-floating">
                                            <input type="text" name="username" class="form-control" id="username" placeholder="Username" autocomplete="off" required />
                                            <label for="username">Username</label>
                                        </div>
                                        <div class="form-floating">
                                            <input type="password" name="password" class="form-control" id="password" placeholder="Password" autocomplete="off" required />
                                            <label for="password">Password</label>
                                            <span class="password-toggle-icon"><i class="fas fa-eye-slash"></i></span>
                                        </div>
                                        <div class="text-end mt-0 small-text">
                                            <a href="/lupa-password"><small>Forgot Password?</small></a>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary tombol-login">Login</button>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">Don't have an account? <a href="/daftar">Create Account</a></p>
                                        </div>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <button id="installPwa" class="btn btn-primary w-100">
                                            Install App
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/login.js') }}?v={{ filemtime(public_path('js/login.js')) }}"></script>
    <script>
        (function() {
            const setTheme = (theme) => {
                const isDark = theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                // Dynamic Favicon Switching
                const favicon = document.getElementById('favicon');
                if (favicon) {
                    favicon.setAttribute('href', isDark ? "{{ asset('img/bimmo_dark_favicon.png') }}" : "{{ asset('img/bimmo_favicon.png') }}");
                }
            }

            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
            setTheme(prefersDark.matches ? 'dark' : 'light');

            prefersDark.addEventListener('change', event => {
                setTheme(event.matches ? 'dark' : 'light');
            });
        })();
    </script>
    <script>
        const installBtn = document.getElementById('installPwa');

        if (installBtn) {
            installBtn.addEventListener('click', () => {
                if (!window.deferredPrompt) {
                    alert('Browser belum mengizinkan instalasi. Mohon tunggu beberapa detik atau pastikan Anda menggunakan Chrome/Edge di Android/Windows.');
                    console.warn('PWA Debug: Clicked install but deferredPrompt is null');
                    return;
                }
                installBtn.style.display = 'none';
                window.deferredPrompt.prompt();
                window.deferredPrompt.userChoice.then((choiceResult) => {
                    console.log('PWA Debug: User choice outcome:', choiceResult.outcome);
                    window.deferredPrompt = null;
                });
            });
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                const swPath = "{{ asset('sw.js') }}";
                navigator.serviceWorker.register(swPath, { scope: '/' })
                    .then(reg => {
                        console.log('PWA Debug: Service Worker registered. Path:', swPath);
                    })
                    .catch(err => console.error('PWA Debug: Service Worker registration FAILED:', err));
            });
        }
    </script>
</body>

</html>