// Fungsi untuk menampilkan toast notification
function showToast(message, type) {
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    const toastId = 'toast-' + Date.now();
    const colors = {
        success: '#012970',  // Biru
        danger: '#dc3545',   // Merah
        warning: '#ffc107',  // Kuning
        info: '#17a2b8',     // Biru muda
        primary: '#007bff',  // Biru
    };

    const bgColor = colors[type] || '#6c757d'; // Default ke abu-abu jika tipe tidak ada

    const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white border-0" style="background-color: ${bgColor};" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

    // Tambahkan toast ke container
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}

$(document).ready(function () {
    new TomSelect("#id_pengeluaran", {
        plugins: ['remove_button'],
        persist: false,
        create: false,
        maxItems: null,
        placeholder: 'Select Expense Type',
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
                data: 'nama_pengeluaran',
                name: 'nama_pengeluaran',
                className: 'text-left',
                defaultContent: '-',
                render: function (data, type, row) {
                    if (type === "display") {
                        if (data && typeof data === "string") {
                            var lines = data.split(","); // ubah \n jadi koma kalau memang datanya koma
                            var table = `
                <table style="width: 100%; border-collapse: collapse;">
                    <colgroup>
                        <col style="width: 30px;">
                        <col>
                    </colgroup>`;

                            lines.forEach(function (line, index) {
                                table += `
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">${index + 1}</td>
                        <td style="border: 1px solid #ddd; padding: 4px;">${line.trim()}</td>
                    </tr>`;
                            });

                            table += "</table>";
                            return table;
                        } else {
                            return "-";
                        }
                    }
                    return data ? data : "-";
                }
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
                    showToast('The budget name must be filled in!', 'danger');
                    return;
                }

                if (formData.persentase_anggaran === '') {
                    showToast('The percentage must be filled in!', 'danger');
                    return;
                }

                $('.tombol-simpan-anggaran').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Process ...');

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    success: function () {
                        showToast('Data saved successfully', 'success');
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
                $('#id_pengeluaran').val(response.result.id_pengeluaran); // Trigger change untuk TomSelect
                simpanAnggaran(id);
            }
        });
    });

    // Handle Delete Pemasukan
    $('body').on('click', '.tombol-del-anggaran', function (e) {
        Swal.fire({
            title: 'Are you sure you want to delete this data?',
            html: 'Deleted data cannot be recovered!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#012970',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel',
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
                        showToast('Data successfully deleted', 'success');
                        $('#anggaranTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        showToast('Data failed to be deleted', 'danger');
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