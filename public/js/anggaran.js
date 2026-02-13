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

    // Set Active Theme in Dropdown
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

    // Global Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Eksekusi awal tema
    applyTheme(skin);
    highlightActiveSkin(skin);
    window.setTheme = setTheme;

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
            <div id="${toastId}" class="toast align-items-center text-white border-0" style="background-color: ${bgColor};" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
        toast.show();
        toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
    }

    // Initialize TomSelect
    let tomSelectInstance;
    if ($('#id_pengeluaran').length) {
        tomSelectInstance = new TomSelect('#id_pengeluaran', {
            plugins: ['remove_button'],
            create: false,
            maxItems: null,
            hideSelected: true,
            closeAfterSelect: false,
            persist: false
        });
    }

    // DataTable Initialization
    const table = $('#anggaranTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/anggaran',
            type: 'GET',
            dataSrc: function (json) {
                // Update Summary Card
                if (json.totalPersentase !== undefined) {
                    $('#totalPersentase').text(json.totalPersentase + '%');

                    // Handle Exceed Message
                    const exceedMsg = $('#exceedMessage');
                    if (json.totalPersentase > 100) {
                        exceedMsg.text('Exceeds 100%!').removeClass('d-none bg-warning text-dark').addClass('bg-danger text-white');
                    } else if (json.totalPersentase < 100 && json.totalPersentase > 0) {
                        if (json.exceedMessage) {
                            exceedMsg.text(json.exceedMessage);
                            if (json.totalPersentase > 100) {
                                exceedMsg.removeClass('d-none bg-warning text-dark').addClass('bg-danger text-white');
                            } else {
                                exceedMsg.removeClass('d-none bg-danger text-white').addClass('bg-warning text-dark');
                            }
                        } else {
                            exceedMsg.addClass('d-none');
                        }
                    } else {
                        exceedMsg.addClass('d-none');
                    }
                }
                return json.data;
            }
        },
        columns: [
            {
                data: 'id_anggaran', // Use id_anggaran !!
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return `<input class="form-check-input check-item" type="checkbox" value="${data}">`;
                }
            },
            { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama_anggaran' },
            {
                data: 'persentase_anggaran',
                className: 'text-center',
                render: function (data) {
                    return data + '%';
                }
            },
            {
                data: 'list_pengeluaran',
                orderable: false,
                render: function (data) {
                    if (!data || data.length === 0) return '<span class="text-muted">-</span>';
                    if (!Array.isArray(data)) return data;

                    let display = data.slice(0, 3).map((item, i) =>
                        `<span class="badge bg-light text-dark border me-1 mb-1">${item}</span>`
                    ).join('');

                    if (data.length > 3) {
                        display += `<span class="badge bg-light text-muted border mb-1">+${data.length - 3} more</span>`;
                    }
                    return display;
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
                className: 'align-middle text-center',
                render: function (data) {
                    return data;
                }
            }
        ]
    });

    // CRUD Functions
    function getFormData() {
        return {
            nama_anggaran: $('#nama_anggaran').val().trim(),
            persentase_anggaran: $('#persentase_anggaran').val().trim(),
            id_pengeluaran: $('#id_pengeluaran').val()
        };
    }

    function validateForm(data) {
        if (!data.nama_anggaran) {
            showToast('Budget Name is required!', 'danger');
            return false;
        }
        if (!data.persentase_anggaran) {
            showToast('Percentage is required!', 'danger');
            return false;
        }
        if (!data.id_pengeluaran || data.id_pengeluaran.length === 0) {
            showToast('Please select at least one Expense Type!', 'danger');
            return false;
        }
        return true;
    }

    function resetForm() {
        $('#nama_anggaran').val('');
        $('#persentase_anggaran').val('');
        if (tomSelectInstance) tomSelectInstance.clear();
        $('#anggaranModal').removeData('id');
        $('#anggaranModalLabel').text('Add New Budget');
        $('.tombol-simpan-anggaran').html('Save').prop('disabled', false);
    }

    function saveBudget(isUpdate = false, id = null) {
        const data = getFormData();
        if (!validateForm(data)) return;

        const url = isUpdate ? '/anggaran/' + id : '/anggaran';
        const type = isUpdate ? 'PUT' : 'POST';

        $('.tombol-simpan-anggaran').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: url,
            type: type,
            data: data,
            success: function (response) {
                showToast(response.message || 'Data saved successfully', 'success');
                $('#anggaranModal').modal('hide');
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let msg = 'Validation Error';
                    const firstKey = Object.keys(errors)[0];
                    if (firstKey) msg = errors[firstKey][0];

                    showToast(msg, 'danger');
                } else {
                    showToast('Failed to save data.', 'danger');
                }
            },
            complete: function () {
                $('.tombol-simpan-anggaran').prop('disabled', false).html('Save');
            }
        });
    }

    // Event Listeners
    $('body').on('click', '.tombol-tambah-anggaran', function (e) {
        e.preventDefault();
        resetForm();
        $('#anggaranModal').modal('show');
    });

    $('body').on('click', '.tombol-edit-anggaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.get('/anggaran/' + id + '/edit', function (res) {
            const anggaran = res.result;

            $('#anggaranModalLabel').text('Edit Budget');
            $('.tombol-simpan-anggaran').html('Update');

            $('#nama_anggaran').val(anggaran.nama_anggaran);
            $('#persentase_anggaran').val(anggaran.persentase_anggaran);

            if (tomSelectInstance) {
                tomSelectInstance.clear();
                // Add and select options
                Object.entries(anggaran.id_pengeluaran).forEach(([key, val]) => {
                    if (!tomSelectInstance.options[key]) {
                        tomSelectInstance.addOption({ value: key, text: val });
                    }
                });
                tomSelectInstance.setValue(Object.keys(anggaran.id_pengeluaran));
            }

            $('#anggaranModal').data('id', id);
            $('#anggaranModal').modal('show');
        }).fail(function () {
            showToast('Failed to fetch data', 'danger');
        });
    });

    $('body').on('click', '.tombol-simpan-anggaran', function (e) {
        e.preventDefault();
        const id = $('#anggaranModal').data('id');
        saveBudget(!!id, id);
    });

    $('#anggaranModal').on('hidden.bs.modal', resetForm);

    $('body').on('click', '.tombol-del-anggaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        Swal.fire({
            title: 'Delete this budget?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/anggaran/' + id,
                    type: 'DELETE',
                    success: function () {
                        showToast('Data deleted successfully', 'success');
                        table.ajax.reload();
                    },
                    error: function () {
                        showToast('Failed to delete data', 'danger');
                    }
                });
            }
        });
    });

    // ----------------------------------------------------------------
    // BULK DELETE LOGIC
    // ----------------------------------------------------------------

    // Check All
    $(document).on('change', '#checkAll', function () {
        const isChecked = $(this).is(':checked');
        $('.check-item').prop('checked', isChecked);
        updateBulkButton();
    });

    // Check Item
    $(document).on('change', '.check-item', function () {
        var total = $('.check-item').length;
        var checked = $('.check-item:checked').length;

        $('#checkAll').prop('checked', total === checked);
        $('#checkAll').prop('indeterminate', checked > 0 && checked < total);
        updateBulkButton();
    });

    // Update Button Visibility
    function updateBulkButton() {
        const checkedCount = $('.check-item:checked').length;
        $('#countSelected').text(checkedCount);
        if (checkedCount > 0) {
            $('#btnBulkDelete').removeClass('d-none');
        } else {
            $('#btnBulkDelete').addClass('d-none');
        }
    }

    // Handle Bulk Delete Click
    $('#btnBulkDelete').on('click', function () {
        const ids = [];
        $('.check-item:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: `Delete ${ids.length} budgets?`,
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
                    url: '/anggaran/bulk-delete',
                    type: 'DELETE',
                    data: { ids: ids },
                    success: function (response) {
                        showToast(response.message, 'success');
                        table.ajax.reload();
                        $('#checkAll').prop('checked', false);
                        $('#btnBulkDelete').addClass('d-none').prop('disabled', false).html(OriginalBtnText);
                    },
                    error: function (xhr) {
                        showToast('Failed to delete selected budgets.', 'danger');
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
});