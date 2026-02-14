<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Register Account - BIMMO</title>
    <link rel="icon" href="{{ asset('img/bimmo_favicon.png') }}" type="image/x-icon">

    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <link href="/img/bimmo_icon.png" rel="icon" />
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
</head>

@include('modal.daftar.index')

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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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
    <script src="{{ asset('js/daftar.js') }}"></script>

</body>

</html>