$(document).ready(function () {
    new TomSelect("#id_pengeluaran", {
        plugins: ['remove_button'],
        persist: false,
        create: false,
        maxItems: null,
        placeholder: 'Pilih jenis pengeluaran',
        render: {
            item: function (data, escape) {
                return '<div class="item">' + escape(data.text) + '</div>';
            }
        }
    });
    $('#anggaranTable').DataTable({
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
            url: '/anggaran',
            type: 'GET',
            dataSrc: function (json) {
                $('#totalPersentase').text(json.totalPersentase.toLocaleString('id-ID'));
                if (json.exceedMessage) {
                    $('#exceedMessage').text(json.exceedMessage).show();
                } else {
                    $('#exceedMessage').hide();
                }
                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            {
                data: 'nama_anggaran', className: 'text-center', render: function (data, type, row) {
                    return data ? data : '-';
                }
            },
            { data: 'persentase_anggaran', className: 'text-center' },
            {
                data: 'nama_pengeluaran',  // pakai accessor virtual tadi
                name: 'nama_pengeluaran',
                defaultContent: '-',
                className: 'text-center',
            },
            { data: 'created_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'updated_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                className: 'text-center',
            }]
    });

    // Handle Save & Update
    function simpanAnggaran(id = '') {
        var url = id ? '/anggaran/' + id : '/anggaran';
        var type = id ? 'PUT' : 'POST';

        isRequesting = true;

        $('#anggaranModal').on('shown.bs.modal', function () {
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

            $('#anggaranModal').off('click', '.tombol-simpan-anggaran').on('click', '.tombol-simpan-anggaran', function () {
                var formData = {
                    nama_anggaran: $('#nama_anggaran').val().trim(),
                    persentase_anggaran: $('#persentase_anggaran').val().trim(),
                    id_pengeluaran: $('#id_pengeluaran').val()  // Ini akan jadi array karena multiple select
                };

                if (formData.nama_anggaran === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Nama Anggaran Harus Diisi!'
                    });
                    return;
                }

                $('.tombol-simpan-anggaran').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');

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
                        $('#anggaranModal').modal('hide');
                        $('#anggaranTable').DataTable().ajax.reload();
                    },
                    complete: function () {
                        $('.tombol-simpan-anggaran').prop('disabled', false).html('Simpan');
                    }
                });
            });

            $('#anggaranModal').on('hidden.bs.modal', function () {
                $('#nama_anggaran').val('');
                $('#persentase_anggaran').val('');
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

    // Handle Add Anggaran
    $('body').on('click', '.tombol-tambah-anggaran', function (e) {
        e.preventDefault();
        $('#anggaranModal').modal('show');
        simpanAnggaran();
    });

    // Handle Edit Anggaran
    $('body').on('click', '.tombol-edit-anggaran', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/anggaran/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                $('#anggaranModal').modal('show');
                $('#nama_anggaran').val(response.result.nama_anggaran);
                $('#persentase_anggaran').val(response.result.persentase_anggaran);
                simpanAnggaran(id);
            }
        });
    });

    // Handle Delete Pemasukan
    $('body').on('click', '.tombol-del-anggaran', function (e) {
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
                    url: '/anggaran/' + id,
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
                        $('#anggaranTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        $('#toastAnggaran').text('Data Gagal dihapus');
                        $('.toast').addClass('bg-danger');
                        $('.toast').toast('show');
                        $('#anggaranTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});

// Handle Dark Mode
document.addEventListener('DOMContentLoaded', function () {
    const darkModeDropdown = document.getElementById('darkModeDropdown');

    // Cek apakah dark mode telah dipilih sebelumnya
    const storedMode = localStorage.getItem('darkMode');
    const isDarkMode = storedMode === 'enabled';

    // Setel status dark mode berdasarkan preferensi sebelumnya
    if (isDarkMode) {
        enableDarkMode();
        darkModeDropdown.value = 'dark';
    }

    darkModeDropdown.addEventListener('change', function () {
        const selectedMode = darkModeDropdown.value;
        if (selectedMode === 'dark') {
            enableDarkMode();
            localStorage.setItem('darkMode', 'enabled');
        } else {
            disableDarkMode();
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
