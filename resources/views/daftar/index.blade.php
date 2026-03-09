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
                                        <h5 class="card-title text-center pb-0 fs-4">
                                            Create an account
                                        </h5>
                                        <p class="text-center small">
                                            Enter your personal details to create an account
                                        </p>
                                    </div>
                                    <!-- csrf agar tidak bisa di cross side -->
                                    <form class="row g-3 needs-validation" action="/daftar" method="post">
                                        @csrf
                                        <div class="form-floating">
                                            <input type="text" name="name" class="form-control rounded-bottom @error('name') is-invalid @enderror" id="yourName" placeholder="Full Name" required />
                                            <label for="yourName">Full Name</label>
                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-floating">
                                            <input type="email" name="email" class="form-control rounded-bottom @error('email') is-invalid @enderror" id="yourEmail" placeholder="Email" required />
                                            <label for="yourEmail">Email</label>
                                            @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-floating">
                                            <input type="text" name="username" class="form-control rounded-bottom @error('username') is-invalid @enderror" id="yourUsername" placeholder="Username" required />
                                            <label for="yourUsername">Username</label>
                                            @error('username')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-floating">
                                            <input type="password" name="password" class="form-control rounded-bottom @error('password') is-invalid @enderror" id="password" placeholder="Password" required />
                                            <label for="password">Password</label>
                                            <span class="password-toggle-icon"><i class="fas fa-eye-slash"></i></span>
                                            @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required />
                                                <label class="form-check-label" for="acceptTerms">I agree and accept the
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#openModal">terms and conditions</a></label>
                                                <div class="invalid-feedback">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary tombol-login" name="/create" type="submit">
                                                Create an account
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">
                                                Already have an account?
                                                <a href="/bimmo">Login</a>
                                            </p>
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
                        <div id="pwaInstructions" class="text-start mt-3">
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
</body>

</html>