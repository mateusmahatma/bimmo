<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Forgot Password - BIMMO</title>
    <link rel="icon" href="{{ asset('img/bimmo_favicon.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('css/style_login.css') }}?v={{ filemtime(public_path('css/style_login.css')) }}" rel="stylesheet" />
    <link href="/css/all.min.css" rel="stylesheet" />
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/main.js') }}"></script>

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
</body>

</html>