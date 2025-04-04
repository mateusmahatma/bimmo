<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Pointech</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="/img/icon_pointech.png" rel="icon" />
    <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="/vendor/quill/quill.snow.css" rel="stylesheet" />
    <link href="/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <link href="/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link href="/vendor/simple-datatables/style.css" rel="stylesheet" />
    <link href="/css/style.css" rel="stylesheet" />
    <link href="/css/all.min.css" rel="stylesheet" />
</head>

<body>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="#" class="logo d-flex align-items-center w-auto">
                                    <img src="/img/icon_pointech.png" alt="" class="icon-pointech" />
                                    <span class="d-none d-lg-block">Pointech</span>
                                </a>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Selamat Datang Kembali</h5>
                                        <p class="text-center small">Masukkan Nama Pengguna & Kata Sandi Anda untuk masuk</p>
                                    </div>
                                    @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                    @endif
                                    <form class="row g-3 needs-validation" action="/login" method="post">
                                        @csrf
                                        <div class="form-floating">
                                            <input type="text" name="username" class="form-control rounded-bottom" id="username" placeholder="Nama Pengguna" autocomplete="off" required />
                                            <label for="username">Nama Pengguna</label>
                                        </div>
                                        <div class="form-floating">
                                            <input type="password" name="password" class="form-control rounded-bottom" id="password" placeholder="Password" autocomplete="off" required />
                                            <label for="password">Kata Sandi</label>
                                            <span class="password-toggle-icon"><i class="fas fa-eye-slash"></i></span>
                                        </div>
                                        <div class="text-end mt-0 small-text">
                                            <a href="/lupa-password"><small>Lupa Kata Sandi ?</small></a>
                                        </div>
                                        <div class="text-center">
                                            <button class="bg-gradient-info tombol-login" type="submit">Masuk</button>
                                        </div>
                                        <div class="socials-row">
                                            <a href="#"><img src="/img/google.png" alt="Google">Masuk dengan Google</a>
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

    <footer>
        <div class="footer-container">
            <p class="footer-text">Copyright © 2024 <strong>@mateusbimahatma</strong>. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2@10') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>