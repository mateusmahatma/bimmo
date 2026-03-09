<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Forgot Password - BIMMO</title>
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

    <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
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
    <link href="/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />

    <style>
        /* Scoped styles to match card-dashboard from style.css */
        .card-dashboard {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
            border: 1px solid #e1e4e8;
            margin-bottom: 24px;
            color: #444;
        }

        .card-header.custom-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e1e4e8;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
            box-shadow: none !important;
        }

        .btn-primary-custom {
            background-color: #012970;
            border-color: #012970;
            color: white;
            border-radius: 50rem;
            padding: 0.375rem 1rem;
            font-size: 0.875rem;
        }

        .btn-primary-custom:hover {
            background-color: #011d50;
            border-color: #011d50;
            color: white;
        }

        body {
            background: #f3f3f3;
        }
    </style>
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
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="card card-dashboard border-0 shadow-sm w-100" style="border-radius: 12px;">
                                <div class="card-header custom-header bg-white py-3">
                                    <div class="text-center mb-3">
                                        <img src="/img/bimmo.png" alt="" style="max-width: 80px;">
                                    </div>
                                    <h5 class="card-title text-center mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Forgot Password</h5>
                                    <p class="text-muted text-center small mb-0 mt-1" style="font-size: 0.85rem;">Enter your email to reset your password.</p>
                                </div>

                                <div class="card-body p-4">
                                    <form id="passwordResetForm" class="row g-3 needs-validation" action="/lupa-password" method="post">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <input type="email" name="email" class="form-control" id="email" placeholder="Email" autocomplete="off" required />
                                            <label for="email">Email</label>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary-custom w-100 tombol-reset shadow-sm" type="submit">Reset Password</button>
                                        </div>
                                        <div class="col-12">
                                            <p class="text-center mt-3 small mb-0">
                                                <a href="/bimmo" class="text-decoration-none">Back to Login</a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button id="installPwa" class="btn btn-primary-custom w-100" style="display:none;">
                                    Install App
                                </button>
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




    <script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/security-measures.js') }}?v={{ filemtime(public_path('js/security-measures.js')) }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('body').on('click', '.tombol-reset', function(e) {
                e.preventDefault();
                // Basic validation
                if (!$('#email').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention',
                        text: 'The email must be filled in',
                        confirmButtonColor: '#012970'
                    });
                    return;
                }

                $('.tombol-reset').prop('disabled', true);
                $('.tombol-reset').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
                var formData = {
                    email: $('#email').val(),
                };

                $.ajax({
                    url: '/lupa-password',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('.tombol-reset').prop('disabled', false);
                        $('.tombol-reset').html('Atur Ulang Kata Sandi');

                        if (response.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.error,
                                confirmButtonColor: '#d33'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.success,
                                confirmButtonColor: '#012970'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '/bimmo';
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        $('.tombol-reset').prop('disabled', false);
                        $('.tombol-reset').html('Atur Ulang Kata Sandi');

                        var errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (xhr.status === 404 && xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });
        });
    </script>
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