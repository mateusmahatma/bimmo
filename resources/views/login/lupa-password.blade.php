<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Lupa Kata Sandi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link href="/img/icon_pointech.png" rel="icon" />

    <!-- Vendor CSS Files -->
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
                        <div class="col-lg-8 col-md-12 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="#" class="logo d-flex align-items-center w-auto">
                                    <img src="/img/icon_pointech.png" alt="" class="icon-pointech" />
                                    <span class="d-none d-lg-block">Pointech</span>
                                </a>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Lupa Kata Sandi</h5>
                                    </div>
                                    <form id="passwordResetForm" class="row g-3 needs-validation" action="/lupa-password" method="post">
                                        @csrf
                                        <div class="form-floating">
                                            <input type="email" name="email" class="form-control rounded-bottom" id="email" placeholder="Email" autocomplete="off" required />
                                            <label for="email">Email</label>
                                        </div>
                                        <div class="col-12">
                                            <button class="bg-gradient-info tombol-reset" type="submit">Atur Ulang Kata Sandi</button>
                                        </div>
                                        <div class="col-12">
                                            <p class="text-end mt-0 small-text">
                                                <a href="/pointech"><small>Kembali ke Masuk</small></a>
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

    <!-- Modal -->
    <div id="passwordModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Informasi Password Baru</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Password baru Anda adalah: <span id="newPassword"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-color2" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2@10') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/login.js') }}"></script>
    <script>
        $(document).ready(function() {
            // $('#passwordResetForm').on('submit', function(e) {
            $('body').on('click', '.tombol-reset', function(e) {
                e.preventDefault();
                $('.tombol-reset').prop('disabled', true);
                $('.tombol-reset').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');
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
                        if (response.error) {
                            alert(response.error);
                        } else {
                            $('.tombol-reset').prop('disabled', false);
                            $('.tombol-reset').html('Reset Password');
                            $('.tombol-reset .spinner-border').remove();
                            $('#newPassword').text(response.newPassword);
                            $('#passwordModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 404) {
                            var response = xhr.responseJSON;
                            if (response && response.error) {
                                $('.tombol-reset').prop('disabled', false);
                                $('.tombol-reset').html('Reset Password');
                                $('.tombol-reset .spinner-border').remove();
                                alert(response.error);
                            } else {
                                $('.tombol-reset').prop('disabled', false);
                                $('.tombol-reset').html('Reset Password');
                                $('.tombol-reset .spinner-border').remove();
                                alert('Terjadi kesalahan. Silakan coba lagi.');
                            }
                        } else {
                            $('.tombol-reset').prop('disabled', false);
                            $('.tombol-reset').html('Reset Password');
                            $('.tombol-reset .spinner-border').remove();
                            alert('Terjadi kesalahan. Silakan coba lagi.');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>