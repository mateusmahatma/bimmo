$(document).ready(function () {
    $('#pemasukanTable').DataTable({
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
            url: '/pemasukan',
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
        var url = id ? '/pemasukan/' + id : '/pemasukan';
        var type = id ? 'PUT' : 'POST';

        isRequesting = true;

        $('#pemasukanModal').on('shown.bs.modal', function () {
            var toastMixin = Swal.mixin({
                toast: true,
                icon: 'success',
                title: 'General Title',
                animation: false,
                position: 'top',
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

            $('#pemasukanModal').off('click', '.tombol-simpan-pemasukan').on('click', '.tombol-simpan-pemasukan', function () {
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

                $('.tombol-simpan-pemasukan').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    success: function () {
                        toastMixin.fire({
                            animation: true,
                            title: 'Data Berhasil disimpan',
                            iconColor: '#012970',
                            customClass: {
                                title: 'swal2-title-create',
                                icon: 'swal2-icon-success',
                            }
                        });
                        $('#pemasukanModal').modal('hide');
                        $('#pemasukanTable').DataTable().ajax.reload();
                    },
                    complete: function () {
                        $('.tombol-simpan-pemasukan').prop('disabled', false).html('Simpan');
                    }
                });
            });

            $('#pemasukanModal').on('hidden.bs.modal', function () {
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

    // Handle Add
    $('body').on('click', '.tombol-tambah-pemasukan', function (e) {
        e.preventDefault();
        $('#pemasukanModal').modal('show');
        simpanPemasukan();
    });

    // Handle Edit
    $('body').on('click', '.tombol-edit-pemasukan', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/pemasukan/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                $('#pemasukanModal').modal('show');
                $('#nama').val(response.result.nama);
                simpanPemasukan(id);
            }
        });
    });

    // Handle Delete
    $('body').on('click', '.tombol-del-pemasukan', function (e) {
        e.preventDefault();
        var toastMixin = Swal.mixin({
            toast: true,
            icon: 'success',
            title: 'General Title',
            animation: false,
            position: 'top',
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
                    url: '/pemasukan/' + id,
                    type: 'DELETE',
                    success: function () {
                        toastMixin.fire({
                            animation: true,
                            title: 'Data Berhasil dihapus',
                            iconColor: '#012970',
                            customClass: {
                                title: 'swal2-title-create',
                                icon: 'swal2-icon-success',
                            }
                        });
                        $('#pemasukanTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        $('#toastPemasukan').text('Data Gagal dihapus');
                        $('.toast').addClass('bg-danger');
                        $('.toast').toast('show');
                        $('#pemasukanTable').DataTable().ajax.reload();
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
