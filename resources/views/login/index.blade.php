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
                                        <button id="installPwa" class="btn btn-outline-primary btn-sm w-100" style="display: none;">
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
        let deferredPrompt;
        const installBtn = document.getElementById('installPwa');

        // Check for iOS
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

        console.log('PWA Debug: Checking installation possibility...');

        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('PWA Debug: beforeinstallprompt fired!');
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI notify the user they can add to home screen
            if (installBtn) {
                installBtn.style.display = 'block';
                console.log('PWA Debug: Install button displayed');
            }
        });

        if (isIOS) {
            console.log('PWA Debug: Running on iOS');
            // iOS doesn't support beforeinstallprompt, but we can show instructions
            // You might want to show a message like "To install, tap the share icon and select 'Add to Home Screen'"
        }

        if (installBtn) {
            installBtn.addEventListener('click', (e) => {
                if (!deferredPrompt) {
                    console.log('PWA Debug: No deferredPrompt found');
                    return;
                }
                // hide our user interface that shows our A2HS button
                installBtn.style.display = 'none';
                // Show the prompt
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                deferredPrompt.userChoice.then((choiceResult) => {
                    console.log('PWA Debug: User choice outcome:', choiceResult.outcome);
                    deferredPrompt = null;
                });
            });
        }

        window.addEventListener('appinstalled', (evt) => {
            console.log('PWA Debug: Bimmo was installed');
            if (installBtn) installBtn.style.display = 'none';
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                const swPath = "{{ asset('sw.js') }}";
                console.log('PWA Debug: Registering Service Worker at:', swPath);
                navigator.serviceWorker.register(swPath)
                    .then(reg => {
                        console.log('PWA Debug: Service Worker registered', reg);
                        // Check if manifest is working
                        if (!document.querySelector('link[rel="manifest"]')) {
                            console.warn('PWA Debug: Manifest link NOT found in DOM!');
                        }
                    })
                    .catch(err => console.error('PWA Debug: Service Worker registration FAILED:', err));
            });
        }
    </script>
</body>

</html>