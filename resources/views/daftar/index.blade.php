<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Register Account - BIMMO</title>
    <link rel="icon" id="favicon" href="{{ asset('img/bimmo_favicon.png') }}" type="image/png">
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ filemtime(public_path('manifest.json')) }}">
    <meta name="theme-color" content="#0984e3">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Bimmo">
    <link rel="apple-touch-icon" href="{{ asset('img/bimmo_favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link href="/img/apple-touch-icon.png" rel="apple-touch-icon" />
    <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="/vendor/quill/quill.snow.css" rel="stylesheet" />
    <link href="/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <link href="/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link href="/vendor/simple-datatables/style.css" rel="stylesheet" />
    <link href="{{ asset('css/style_login.css') }}?v={{ filemtime(public_path('css/style_login.css')) }}" rel="stylesheet" />
    <link href="/css/all.min.css" rel="stylesheet" />
    <style>
        /* Anti screenshot/capture CSS */
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media print {
            body {
                display: none !important;
            }
        }
    </style>
</head>

@include('modal.daftar.index')

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

        <!-- Form Daftar Kanan -->
        <div class="col-12 col-lg-6 d-flex flex-column justify-content-center align-items-center bg-white shadow-lg z-3">
            <div class="w-100 px-4 px-sm-5" style="max-width: 480px; margin-top: auto;">
                <!-- Muncul di Mobile/Tablet saja -->
                <div class="text-center mb-3 d-lg-none">
                    <img src="/img/bimmo.png" alt="Bimmo Logo" class="mb-2" style="max-width: 90px;">
                    <h4 class="fw-bold" style="color: var(--bs-primary);">Bimmo Portal</h4>
                </div>
                
                <div class="mb-3 mt-2 mt-lg-3 text-center text-lg-start">
                    <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.5px;">Registrasi Akun</h4>
                    <p class="text-muted small mb-0">Lengkapi data diri Anda untuk membuat akun baru.</p>
                </div>

                <form class="row g-2 needs-validation" action="/daftar" method="post">
                    @csrf
                    
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" name="name" class="form-control rounded-3 py-2 px-3 h-auto @error('name') is-invalid @enderror" id="yourName" placeholder="Full Name" value="{{ old('name') }}" required style="border-color: #dee2e6; box-shadow: none;" onfocus="this.style.borderColor='var(--bs-primary)'; this.style.boxShadow='0 0 0 0.25rem rgba(1,41,112,0.1)';" onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none';" />
                            <label for="yourName" class="text-muted py-2 px-3" style="font-size: 0.85rem">Full Name</label>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating">
                            <input type="email" name="email" class="form-control rounded-3 py-2 px-3 h-auto @error('email') is-invalid @enderror" id="yourEmail" placeholder="Email" value="{{ old('email') }}" required style="border-color: #dee2e6; box-shadow: none;" onfocus="this.style.borderColor='var(--bs-primary)'; this.style.boxShadow='0 0 0 0.25rem rgba(1,41,112,0.1)';" onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none';" />
                            <label for="yourEmail" class="text-muted py-2 px-3" style="font-size: 0.85rem">Email</label>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" name="username" class="form-control rounded-3 py-2 px-3 h-auto @error('username') is-invalid @enderror" id="yourUsername" placeholder="Username" value="{{ old('username') }}" required style="border-color: #dee2e6; box-shadow: none;" onfocus="this.style.borderColor='var(--bs-primary)'; this.style.boxShadow='0 0 0 0.25rem rgba(1,41,112,0.1)';" onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none';" />
                            <label for="yourUsername" class="text-muted py-2 px-3" style="font-size: 0.85rem">Username</label>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating position-relative">
                            <input type="password" name="password" class="form-control rounded-3 py-2 px-3 pe-5 h-auto @error('password') is-invalid @enderror" id="password" placeholder="Password" required style="border-color: #dee2e6; box-shadow: none;" onfocus="this.style.borderColor='var(--bs-primary)'; this.style.boxShadow='0 0 0 0.25rem rgba(1,41,112,0.1)';" onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none';" />
                            <label for="password" class="text-muted py-2 px-3" style="font-size: 0.85rem">Password</label>
                            <span class="password-toggle-icon position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer; z-index: 10; color: #6c757d;">
                                <i class="fas fa-eye-slash hover-text-primary transition-all"></i>
                            </span>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="form-check form-check-inline-custom mb-1">
                            <input class="form-check-input" style="cursor: pointer; width: 0.9rem; height: 0.9rem; margin-top: 0.25rem;" name="terms" type="checkbox" value="" id="acceptTerms" required />
                            <label class="form-check-label text-muted ms-1" style="cursor: pointer; font-size: 0.8rem;" for="acceptTerms">Saya setuju dengan 
                                <a href="#" data-bs-toggle="modal" data-bs-target="#openModal" class="fw-semibold text-primary text-decoration-none">Syarat & Ketentuan</a>
                            </label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary w-100 py-2 fw-bold tombol-login" name="/create" type="submit" style="border-radius: 8px; font-size: 1rem; box-shadow: 0 4px 12px rgba(1, 41, 112, 0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
                            Buat Akun
                        </button>
                    </div>
                    
                    <div class="col-12 text-center mt-2">
                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">Sudah memiliki akun? <a href="/bimmo" class="fw-semibold text-primary text-decoration-none">Masuk di sini</a></p>
                    </div>
                </form>

                <div class="mt-3 pt-2 border-top text-center">
                    <button id="installPwa" class="btn btn-outline-secondary w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 py-1" style="display: none; transition: all 0.3s ease; font-size: 0.85rem;">
                        <i class="bi bi-download"></i> Instal Aplikasi PWA
                    </button>
                </div>
            </div>
            
            <div class="mt-auto py-2 text-center w-100">
                <small class="text-muted" style="font-size: 0.75rem;">&copy; <script>document.write(new Date().getFullYear())</script> Bimmo. All rights reserved.</small>
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

    <!-- Vendor JS Files -->
    <script src="/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/vendor/chart.js/chart.umd.js"></script>
    <script src="/vendor/echarts/echarts.min.js"></script>
    <script src="/vendor/quill/quill.min.js"></script>
    <script src="/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/vendor/tinymce/tinymce.min.js"></script>
    <script src="/vendor/php-email-form/validate.js"></script>
    <script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2@10') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/security-measures.js') }}?v={{ filemtime(public_path('js/security-measures.js')) }}"></script>
    <script src="{{ asset('js/daftar.js') }}"></script>

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
    @include('components.toast')
</body>


</html>