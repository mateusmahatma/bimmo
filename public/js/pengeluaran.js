$(document).ready(function () {
    // Theme Handler
    const skin = window.userSkin || 'auto';
    const updateSkinUrl = window.updateSkinUrl;
    const csrfToken = window.csrfToken;

    function applyTheme(mode) {
        if (mode === 'light' || mode === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', mode);
        } else {
            document.documentElement.removeAttribute('data-bs-theme'); // auto
        }
        document.dispatchEvent(new Event("themeChanged"));
    }

    function highlightActiveSkin(mode) {
        document.querySelectorAll('.dropdown-item').forEach(el => {
            el.classList.remove('active');
            if (el.getAttribute('onclick') === `setTheme('${mode}')`) {
                el.classList.add('active');
            }
        });
    }

    function setTheme(mode) {
        applyTheme(mode);
        highlightActiveSkin(mode);

        if (updateSkinUrl) {
            fetch(updateSkinUrl, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ skin: mode })
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) alert("Gagal menyimpan tema.");
                })
                .catch(err => console.error("Gagal update tema:", err));
        }
    }

    // Eksekusi awal tema
    applyTheme(skin);
    highlightActiveSkin(skin);
    window.setTheme = setTheme;

    const table = $('#pengeluaranTable').DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        ajax: {
            url: '/pengeluaran',
            type: 'GET',
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                searchable: false,
                className: 'align-middle text-center',
                render: function (data, type, row) {
                    return `<div class="form-check d-flex justify-content-center"><input class="form-check-input check-item" type="checkbox" value="${data}" style="cursor: pointer;"></div>`;
                }
            },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'align-middle text-center text-secondary fw-medium' },
            {
                data: 'nama',
                className: 'align-middle fw-semibold text-dark',
                render: function (data) {
                    return `<span style="font-size: 0.95rem;">${data}</span>`;
                }
            },
            {
                data: 'created_at',
                className: 'align-middle text-center text-muted small',
                render: function (data) {
                    return `<span style="font-family: 'Consolas', monospace;">${data}</span>`;
                }
            },
            {
                data: 'updated_at',
                className: 'align-middle text-center text-muted small',
                render: function (data) {
                    return `<span style="font-family: 'Consolas', monospace;">${data}</span>`;
                }
            },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                className: "align-middle text-center",
                render: function (data) {
                    return data;
                },
            }
        ]
    });

    // Toast Notification
    function showToast(message, type) {
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const colors = {
            success: '#012970',
            danger: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8',
            primary: '#007bff',
        };

        const bgColor = colors[type] || '#6c757d';

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

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }

    // Function to handle Save & Update
    function simpanPengeluaran(id = '') {
        var url = id ? '/pengeluaran/' + id : '/pengeluaran';
        var type = id ? 'PUT' : 'POST';

        var formData = {
            nama: $('#nama').val().trim()
        };

        if (formData.nama === '') {
            showToast('Category name is required!', 'danger');
            return;
        }

        $('.tombol-simpan-pengeluaran').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing ...');

        $.ajax({
            url: url,
            type: type,
            data: formData,
            success: function () {
                showToast('Data saved successfully', 'success');
                $('#pengeluaranModal').modal('hide');
                table.ajax.reload();
            },
            complete: function () {
                $('.tombol-simpan-pengeluaran').prop('disabled', false).html('Save');
            }
        });
    }

    // Handle Create Pengeluaran
    $('body').on('click', '.tombol-tambah-pengeluaran', function (e) {
        e.preventDefault();
        $('#nama').val(''); // Clear input
        $('#pengeluaranModal').modal('show');

        $('#pengeluaranModal').off('click', '.tombol-simpan-pengeluaran')
            .on('click', '.tombol-simpan-pengeluaran', () => simpanPengeluaran());
    });

    // Handle Edit Pengeluaran
    $('body').on('click', '.tombol-edit-pengeluaran', function (e) {
        var id = $(this).data('id');

        $.ajax({
            url: '/pengeluaran/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                $('#nama').val(response.result.nama);
                $('#pengeluaranModal').modal('show');

                $('#pengeluaranModal').off('click', '.tombol-simpan-pengeluaran')
                    .on('click', '.tombol-simpan-pengeluaran', () => simpanPengeluaran(id));
            }
        });
    });

    // Single Delete (using SweetAlert now)
    $('body').on('click', '.tombol-del-pengeluaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            html: 'You will not be able to recover this data!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: { popup: 'dark-mode' }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/pengeluaran/${id}`,
                    type: 'DELETE',
                    success: () => {
                        showToast('Data deleted successfully', 'success');
                        table.ajax.reload();
                    },
                    error: () => {
                        showToast('Delete failed, data might be in use', 'danger');
                        table.ajax.reload();
                    }
                });
            }
        });
    });

    // Bulk Delete Logic
    $(document).on('change', '#checkAll', function () {
        const isChecked = $(this).is(':checked');
        $('.check-item').prop('checked', isChecked);
        updateBulkButton();
    });

    $(document).on('change', '.check-item', function () {
        var total = $('.check-item').length;
        var checked = $('.check-item:checked').length;

        $('#checkAll').prop('checked', total === checked);
        $('#checkAll').prop('indeterminate', checked > 0 && checked < total);
        updateBulkButton();
    });

    function updateBulkButton() {
        const checkedCount = $('.check-item:checked').length;
        $('#countSelected').text(checkedCount);
        if (checkedCount > 0) {
            $('#btnBulkDelete').removeClass('d-none');
        } else {
            $('#btnBulkDelete').addClass('d-none');
        }
    }

    $('#btnBulkDelete').on('click', function () {
        const ids = [];
        $('.check-item:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: `Delete ${ids.length} categories?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete selected!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const OriginalBtnText = $(this).html();
                $(this).html('<span class="spinner-border spinner-border-sm"></span> Deleting...').prop('disabled', true);

                $.ajax({
                    url: '/pengeluaran/bulk-delete',
                    type: 'DELETE',
                    data: { ids: ids },
                    success: function (response) {
                        showToast(response.message, 'success');
                        table.ajax.reload();
                        $('#checkAll').prop('checked', false);
                        $('#btnBulkDelete').addClass('d-none').prop('disabled', false).html(OriginalBtnText);
                    },
                    error: function (xhr) {
                        showToast('Failed to delete selected categories.', 'danger');
                        $('#btnBulkDelete').prop('disabled', false).html(OriginalBtnText);
                    }
                });
            }
        });
    });

    // Reset check all on page change
    table.on('draw', function () {
        $('#checkAll').prop('checked', false);
        $('#checkAll').prop('indeterminate', false);
        updateBulkButton();
    });

    // Global Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});