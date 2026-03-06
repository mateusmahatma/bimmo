<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Bimmo</title>
    <link rel="icon" id="favicon" href="{{ asset('img/bimmo_favicon.png') }}" type="image/png">
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ filemtime(public_path('manifest.json')) }}">
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
        // PWA Diagnostic Suite v3
        window.deferredPrompt = null;
        console.log('PWA Debug: Starting advanced diagnostics...');

        // 1. Listen for the install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            console.info('%c PWA Debug: SUCCESS! beforeinstallprompt event fired.', 'background: #222; color: #bada55');
            e.preventDefault();
            window.deferredPrompt = e;
            const btn = document.getElementById('installPwa');
            if (btn) btn.style.display = 'block';
        });

        // 2. Check current mode & existence
        window.addEventListener('load', () => {
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            if (isStandalone) {
                console.warn('PWA Debug: STATUS - Aplikasi sudah berjalan dalam mode STANDALONE (sudah terinstal).');
            }

            // 3. Verify Manifest and Icons
            const manifestLink = document.querySelector('link[rel="manifest"]');
            if (!manifestLink) {
                console.error('PWA Debug: ERROR - Manifest link hilang di <head>!');
            } else {
                console.log('PWA Debug: Manifest ditemukan:', manifestLink.href);
                fetch(manifestLink.href)
                    .then(r => r.json())
                    .then(json => console.log('PWA Debug: Manifest valid & terbaca:', json.short_name))
                    .catch(e => console.error('PWA Debug: Manifest TIDAK valid atau tidak terbaca!', e));
            }

            // 4. Trace why event might not fire
            setTimeout(() => {
                if (!window.deferredPrompt && !isStandalone) {
                    console.info('PWA Debug: INFO - Event belum muncul setelah 10 detik. Kemungkinan: 1) App sudah terinstal di OS, 2) Kriteria PWA belum 100% dipenuhi di tab Application > Manifest, atau 3) Browser butuh interaksi user lebih banyak.');
                }
            }, 10000);
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

        <!-- PWA Manual Install Modal -->
        <div class="modal fade" id="pwaInstructionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cara Instal Aplikasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p id="pwaDeviceText">Browser Anda belum mengizinkan instalasi otomatis.</p>
                        <div id="pwaInstructions" class="text-start mt-3">
                            <strong>Instal Manual:</strong>
                            <ul class="mt-2" id="instructionList">
                                <li><strong>Chrome/Edge (Android/PC):</strong> Klik titik tiga di pojok kanan atas, lalu cari menu <strong>"Instal aplikasi"</strong> atau <strong>"Add to Home screen"</strong>.</li>
                                <li><strong>Safari (iOS/iPhone):</strong> Tekan tombol <strong>Share</strong> (ikon kotak dengan panah ke atas), lalu pilih <strong>"Add to Home Screen"</strong>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
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
                if (window.deferredPrompt) {
                    // Use the automatic prompt if available
                    window.deferredPrompt.prompt();
                    window.deferredPrompt.userChoice.then((choiceResult) => {
                        console.log('PWA Debug: User choice outcome:', choiceResult.outcome);
                        window.deferredPrompt = null;
                        if (choiceResult.outcome === 'accepted') {
                            installBtn.style.display = 'none';
                        }
                    });
                } else {
                    // Show manual instructions modal as fallback
                    console.warn('PWA Debug: Using manual fallback');
                    const modal = new bootstrap.Modal(document.getElementById('pwaInstructionModal'));
                    
                    // Detect device for specific message
                    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                    const deviceText = document.getElementById('pwaDeviceText');
                    const list = document.getElementById('instructionList');
                    
                    if (isIOS) {
                        deviceText.innerText = "Khusus pengguna iPhone/iPad, instalasi harus dilakukan secara manual melalui Safari.";
                        list.innerHTML = '<li>Tekan tombol <strong>Share</strong> (ikon kotak dengan panah ke atas) di bawah layar.</li><li>Gulir ke bawah dan pilih <strong>"Add to Home Screen"</strong>.</li>';
                    }
                    
                    modal.show();
                }
            });
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                const swPath = "{{ asset('sw.js') }}";
                navigator.serviceWorker.register(swPath, { scope: '/' })
                    .then(reg => {
                        console.log('PWA Debug: Service Worker registered. Path:', swPath);
                        reg.update(); // Force update check
                    })
                    .catch(err => console.error('PWA Debug: Service Worker registration FAILED:', err));
            });
        }
    </script>
</body>

</html>