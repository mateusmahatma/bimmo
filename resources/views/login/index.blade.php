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
    <link href="/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="{{ asset('css/style_login.css') }}?v={{ filemtime(public_path('css/style_login.css')) }}" rel="stylesheet" />
    <link href="/css/all.min.css" rel="stylesheet" />
    <style>
        /* Protection Active State */
        body.protection-active {
            overflow: hidden !important;
        }

        body.protection-active > *:not(#protection-overlay):not(script) {
            filter: blur(25px) !important;
            opacity: 0.3 !important;
            pointer-events: none !important;
            user-select: none !important;
        }

        @media print {
            body {
                display: none !important;
            }
        }

        * {
            user-select: none !important;
            -webkit-user-drag: none !important;
        }
    </style>
    <script>
        window.deferredPrompt = null;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            window.deferredPrompt = e;
            const btn = document.getElementById('installPwa');
            if (btn) btn.style.display = 'block';
        });

        window.addEventListener('load', () => {
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        });
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
    <main class="d-flex w-100 min-vh-100">
        <!-- Banner Kiri (Corporate Look) -->
        <div class="d-none d-lg-flex col-lg-6 flex-column justify-content-center align-items-center text-white position-relative" style="background: linear-gradient(135deg, var(--bs-primary) 0%, #001233 100%); overflow: hidden;">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at top right, rgba(255,255,255,0.1) 0%, transparent 50%); pointer-events: none;"></div>
            <div class="text-center p-5 z-2">
                <img src="/img/bimmo.png" alt="Bimmo Logo" class="mb-4" style="max-width: 180px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
                <h1 class="display-5 fw-bold mb-3" style="letter-spacing: -0.5px;">Bimmo Portal</h1>
                <p class="lead" style="color: rgba(255,255,255,0.85); font-weight: 300;">Manajemen Keuangan & Aset Terpadu</p>
            </div>
        </div>

        <!-- Form Login Kanan -->
        <div class="col-12 col-lg-6 d-flex flex-column justify-content-center align-items-center bg-white shadow-lg z-3">
            <div class="w-100 px-4 px-sm-5" style="max-width: 480px; margin-top: auto;">
                <!-- Muncul di Mobile/Tablet saja -->
                <div class="text-center mb-4 d-lg-none">
                    <img src="/img/bimmo.png" alt="Bimmo Logo" class="mb-2" style="max-width: 100px;">
                    <h3 class="fw-bold" style="color: var(--bs-primary);">Bimmo Portal</h3>
                </div>
                
                <div class="mt-2 mt-lg-3 mb-4 text-center text-lg-start">
                    <h3 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.5px;">Selamat Datang</h3>
                    <p class="text-muted small">Silakan masukkan kredensial Anda untuk melanjutkan akses ke sistem.</p>
                </div>

                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                <form class="row g-3 needs-validation" action="/login" method="post">
                    @csrf
                    <div class="col-12">
                        <div class="form-floating mb-1">
                            <input type="text" name="username" class="form-control rounded-3" id="username" placeholder="Username" autocomplete="off" required style="border-color: #dee2e6; box-shadow: none;" onfocus="this.style.borderColor='var(--bs-primary)'; this.style.boxShadow='0 0 0 0.25rem rgba(1,41,112,0.1)';" onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none';" />
                            <label for="username" class="text-muted">Username</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating position-relative">
                            <input type="password" name="password" class="form-control rounded-3 pe-5" id="password" placeholder="Password" autocomplete="off" required style="border-color: #dee2e6; box-shadow: none;" onfocus="this.style.borderColor='var(--bs-primary)'; this.style.boxShadow='0 0 0 0.25rem rgba(1,41,112,0.1)';" onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none';" />
                            <label for="password" class="text-muted">Password</label>
                            <span class="password-toggle-icon position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer; z-index: 10; color: #6c757d;">
                                <i class="fas fa-eye-slash hover-text-primary transition-all"></i>
                            </span>
                        </div>
                        <div class="text-end mt-1">
                            <a href="/lupa-password" class="text-decoration-none small text-primary fw-semibold">Lupa Password?</a>
                        </div>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold tombol-login" style="border-radius: 8px; font-size: 1.05rem; box-shadow: 0 4px 12px rgba(1, 41, 112, 0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">Masuk ke Portal</button>
                    </div>
                    
                    <div class="col-12 text-center mt-2">
                        <p class="small mb-0 text-muted">Belum memiliki akun? <a href="/daftar" class="fw-semibold text-primary text-decoration-none">Daftar Sekarang</a></p>
                    </div>
                </form>

                <div class="mt-4 pt-3 border-top text-center">
                    <button id="installPwa" class="btn btn-outline-secondary w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 py-2" style="display: none; transition: all 0.3s ease;">
                        <i class="bi bi-download"></i> Instal Aplikasi PWA
                    </button>
                </div>
            </div>
            
            <div class="mt-auto py-3 text-center w-100">
                <small class="text-muted">&copy; <script>document.write(new Date().getFullYear())</script> Bimmo. All rights reserved.</small>
            </div>
        </div>

        <!-- PWA Manual Install Modal -->
        <div class="modal fade" id="pwaInstructionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold" style="color: var(--bs-primary);">Cara Instal Aplikasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <p class="text-muted small mb-3 text-center" id="pwaDeviceText">Ikuti langkah sederhana berikut untuk menginstal Bimmo ke perangkat Anda.</p>
                        <div id="pwaInstructions" class="bg-light p-3 rounded-3">
                            <ul class="mb-0 ps-3 text-secondary small" id="instructionList" style="line-height: 1.6;">
                                <li class="mb-2"><strong>Chrome/Edge (Android/PC):</strong> Klik titik tiga di pojok kanan atas, lalu cari menu <strong>"Instal aplikasi"</strong> atau <strong>"Add to Home screen"</strong>.</li>
                                <li><strong>Safari (iOS/iPhone):</strong> Tekan tombol <strong>Share</strong> (ikon kotak dengan panah ke atas), lalu pilih <strong>"Add to Home Screen"</strong>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 justify-content-center">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
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
    <script src="{{ asset('js/security-measures.js') }}?v={{ filemtime(public_path('js/security-measures.js')) }}"></script>
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
                    window.deferredPrompt.prompt();
                    window.deferredPrompt.userChoice.then((choiceResult) => {
                        window.deferredPrompt = null;
                        if (choiceResult.outcome === 'accepted') {
                            installBtn.style.display = 'none';
                        }
                    });
                } else {
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
                        reg.update();
                    })
                    .catch(err => {});
            });
        }
    </script>
</body>

</html>