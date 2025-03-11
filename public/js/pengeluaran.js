$(document).ready(function () {
    $('#pengeluaranTable').DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        language: {
            processing: '<div class="loader-container"><div class="loader"></div></div>'
        },
        ajax: {
            url: '/pengeluaran',
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama', className: 'text-center' },
            { data: 'created_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'updated_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'aksi', }
        ]
    });

    // Function to handle Save & Update
    function simpanPemasukan(id = '') {
        var url = id ? '/pengeluaran/' + id : '/pengeluaran';
        var type = id ? 'PUT' : 'POST';

        isRequesting = true;

        $('#pengeluaranModal').on('shown.bs.modal', function () {
            var toastMixin = Swal.mixin({
                toast: true,
                icon: 'success',
                title: 'General Title',
                animation: false,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                },
                customClass: {
                    title: 'swal2-title-create',
                    popup: 'swal2-popup-create',
                    icon: 'swal2-icon-success'
                }
            });

            $('#pengeluaranModal').off('click', '.tombol-simpan-pengeluaran').on('click', '.tombol-simpan-pengeluaran', function () {
                var formData = {
                    nama: $('#nama').val().trim()
                };

                if (formData.nama === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Nama Harus Diisi!'
                    });
                    return;
                }

                $('.tombol-simpan-pengeluaran').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    success: function () {
                        toastMixin.fire({
                            animation: true,
                            title: 'Data Berhasil disimpan',
                            iconColor: '#ffffff'
                        });
                        $('#pengeluaranModal').modal('hide');
                        $('#pengeluaranTable').DataTable().ajax.reload();
                    },
                    complete: function () {
                        $('.tombol-simpan-pengeluaran').prop('disabled', false).html('Simpan');
                    }
                });
            });

            $('#pengeluaranModal').on('hidden.bs.modal', function () {
                $('#nama').val('');
            });
        });
    }

    // Global Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let isRequesting = false;

    // Handle Add Pemasukan
    $('body').on('click', '.tombol-tambah-pengeluaran', function (e) {
        e.preventDefault();
        $('#pengeluaranModal').modal('show');
        simpanPemasukan();
    });

    // Handle Edit Pemasukan
    $('body').on('click', '.tombol-edit-pengeluaran', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/pengeluaran/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                $('#pengeluaranModal').modal('show');
                $('#nama').val(response.result.nama);
                simpanPemasukan(id);
            }
        });
    });

    // Handle Delete Pemasukan
    $('body').on('click', '.tombol-del-pengeluaran', function (e) {
        e.preventDefault();
        var toastMixin = Swal.mixin({
            toast: true,
            icon: 'success',
            title: 'General Title',
            animation: false,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: false,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });


        Swal.fire({
            title: 'Yakin mau hapus data ini?',
            html: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'dark-mode'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                $.ajax({
                    url: '/pengeluaran/' + id,
                    type: 'DELETE',
                    success: function () {
                        toastMixin.fire({
                            animation: true,
                            title: 'Data Berhasil dihapus',
                            customClass: {
                                title: 'swal2-title-create',
                                popup: 'swal2-popup-create',
                            },
                            iconColor: '#ffffff'
                        });
                        $('#pengeluaranTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        $('#toastPengeluaran').text('Data Gagal dihapus');
                        $('.toast').addClass('bg-danger');
                        $('.toast').toast('show');
                        $('#pengeluaranTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const darkModeDropdown = document.getElementById('darkModeDropdown');

    const storedMode = localStorage.getItem('darkMode');
    const isDarkMode = storedMode === 'enabled';

    if (isDarkMode) {
        enableDarkMode();
        darkModeDropdown.style.color = 'white';
        darkModeDropdown.value = 'dark';
    }

    darkModeDropdown.addEventListener('change', function () {
        const selectedMode = darkModeDropdown.value;
        if (selectedMode === 'dark') {
            enableDarkMode();
            darkModeDropdown.style.color = 'white';
            localStorage.setItem('darkMode', 'enabled');
        } else {
            disableDarkMode();
            darkModeDropdown.style.color = '';
            localStorage.setItem('darkMode', null);
        }
    });

    function enableDarkMode() {
        document.body.classList.add('dark-mode');
    }

    function disableDarkMode() {
        document.body.classList.remove('dark-mode');
    }
});
