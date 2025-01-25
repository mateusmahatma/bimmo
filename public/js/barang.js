$(document).ready(function () {
    var barangTable = $('#barangTable').DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        scrollX: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        language: {
            processing: '<div class="loader-container"><div class="loader"></div></div>'
        },
        ajax: {
            url: '/barang',
            type: 'GET',
            data: function (d) {
                d.status = $('select[name="status"]').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama_barang', className: 'text-center' },
            { data: 'nama_toko', className: 'text-center' },
            { data: 'harga', className: 'text-center' },
            { data: 'jumlah', className: 'text-center' },
            {
                data: 'status', className: 'text-center', render: function (data) {
                    var badgeClass = (data === 'terbeli') ? 'badge-success' : 'badge-danger';
                    var badgeText = (data === 'terbeli') ? 'Terbeli' : 'Belum Terbeli';
                    return '<span class=" ' + badgeClass + '">' + badgeText + '</span>';
                }
            },
            { data: 'created_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'updated_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'aksi', }
        ]
    });

    $('select[name="status"]').on('change', function () {
        barangTable.ajax.reload();
    });

    function simpanBarang(id = '') {
        var url = id ? '/barang/' + id : '/barang';
        var type = id ? 'PUT' : 'POST';

        isRequesting = true;

        $('#barangModal').on('shown.bs.modal', function () {
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

            $('#barangModal').off('click', '.tombol-simpan-barang').on('click', '.tombol-simpan-barang', function () {
                var formData = {
                    nama_barang: $('#nama_barang').val().trim(),
                    status: $('#status').val(),
                    nama_toko: $('#nama_toko').val().trim(),
                    harga: parseFloat($('#harga').val()),
                    jumlah: parseInt($('#jumlah').val())
                };

                if (formData.nama_barang === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Nama Barang Harus Diisi!'
                    });
                    return;
                }

                $('.tombol-simpan-barang').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');

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
                        $('#barangModal').modal('hide');
                        $('#barangTable').DataTable().ajax.reload();
                    },
                    complete: function () {
                        $('.tombol-simpan-barang').prop('disabled', false).html('Simpan');
                    }
                });
            });

            $('#barangModal').on('hidden.bs.modal', function () {
                $('#nama_barang').val('');
                $('#status').val('');
                $('#nama_toko').val('');
                $('#harga').val('');
                $('#jumlah').val('');
            });
        });
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let isRequesting = false;

    $('body').on('click', '.tombol-tambah-barang', function (e) {
        e.preventDefault();
        $('#barangModal').modal('show');
        simpanBarang();
    });

    $('body').on('click', '.tombol-edit-barang', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/barang/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                $('#barangModal').modal('show');
                $('#nama_barang').val(response.result.nama_barang);
                $('#status').val(response.result.status);
                $('#nama_toko').val(response.result.nama_toko);
                $('#harga').val(parseFloat(response.result.harga));
                $('#jumlah').val(parseInt(response.result.jumlah));
                simpanBarang(id);
            }
        });
    });

    $('body').on('click', '.tombol-del-barang', function (e) {
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
                    url: '/barang/' + id,
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
                        $('#barangTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        $('#toastBarang').text('Data Gagal dihapus');
                        $('.toast').addClass('bg-danger');
                        $('.toast').toast('show');
                        $('#barangTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});
