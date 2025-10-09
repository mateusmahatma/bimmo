<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>BIMMO</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="/img/bimmo_icon.png" rel="icon" />
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
                                        <h5 class="card-title text-center pb-0 fs-4">Selamat datang kembali</h5>
                                        <p class="text-center small">Masukkan nama pengguna & kata sandi Anda untuk masuk</p>
                                    </div>
                                    @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                    @endif
                                    <form class="row g-3 needs-validation" action="/login" method="post">
                                        @csrf
                                        <div class="form-floating">
                                            <input type="text" name="username" class="form-control" id="username" placeholder="Nama pengguna" autocomplete="off" required />
                                            <label for="username">Nama pengguna</label>
                                        </div>
                                        <div class="form-floating">
                                            <input type="password" name="password" class="form-control" id="password" placeholder="Kata sandi" autocomplete="off" required />
                                            <label for="password">Kata sandi</label>
                                            <span class="password-toggle-icon"><i class="fas fa-eye-slash"></i></span>
                                        </div>
                                        <div class="text-end mt-0 small-text">
                                            <a href="/lupa-password"><small>Lupa Kata Sandi?</small></a>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary tombol-login">Masuk</button>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">Tidak memiliki akun? <a href="/daftar">Buat Akun</a></p>
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
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/login.js') }}?v={{ filemtime(public_path('js/login.js')) }}"></script>
</body>

</html>