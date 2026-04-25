$(document).ready(function () {
    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let currentSort = 'created_at';
    let currentDirection = 'desc';
    let currentSearch = '';

    if (!window.ANGGARAN_BASE_URL) {
        return;
    }

    const baseUrl = String(window.ANGGARAN_BASE_URL).replace(/\/$/, '');

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
    function initTomSelect() {
        if ($('#id_pengeluaran').length && !$('#id_pengeluaran')[0].tomselect) {
            tomSelectInstance = new TomSelect('#id_pengeluaran', {
                plugins: ['remove_button'],
                create: false,
                maxItems: null,
                hideSelected: true,
                closeAfterSelect: false,
                persist: false
            });
        }
    }
    initTomSelect();

    // Fetch Data Function
    function fetchAnggaran(url = baseUrl) {
        const urlObj = new URL(url, window.location.origin);
        if (currentSearch) urlObj.searchParams.set('search', currentSearch);
        urlObj.searchParams.set('sort', currentSort);
        urlObj.searchParams.set('direction', currentDirection);

        $('#tableContainer').addClass('opacity-50');

        $.ajax({
            url: urlObj.toString(),
            type: 'GET',
            success: function (response) {
                $('#tableContainer').html(response.html).removeClass('opacity-50');
                updateSummary(response.totalPersentase, response.exceedMessage);
                updateBulkButton();
            },
            error: function () {
                showToast('Gagal mengambil data.', 'danger');
                $('#tableContainer').removeClass('opacity-50');
            }
        });
    }

    function updateSummary(total, exceedMessage) {
        $('#totalPersentase').text(total + '%');
        $('#totalPersentaseLabel').text(total + '% / 100%');

        const progressBar = $('#totalAllocationBar');
        if (progressBar.length) {
            progressBar.css('width', Math.min(total, 100) + '%');
            progressBar.attr('aria-valuenow', total);
            if (total > 100) {
                progressBar.removeClass('bg-primary').addClass('bg-danger');
            } else {
                progressBar.removeClass('bg-danger').addClass('bg-primary');
            }
        }

        const exceedMsg = $('#exceedMessage');
        if (total > 100) {
            exceedMsg.text('Melebihi 100%!').removeClass('d-none bg-warning text-dark').addClass('bg-danger text-white');
        } else if (total < 100 && total > 0) {
            if (exceedMessage) {
                exceedMsg.text(exceedMessage).removeClass('d-none bg-danger text-white').addClass('bg-warning text-dark');
            } else {
                exceedMsg.addClass('d-none');
            }
        } else {
            exceedMsg.addClass('d-none');
        }
    }

    // Search with Debounce
    let searchTimer;
    $(document).on('input', '#entrySearch', function () {
        clearTimeout(searchTimer);
        currentSearch = $(this).val();
        searchTimer = setTimeout(() => {
            fetchAnggaran();
        }, 500);
    });

    // Sorting
    $('#tableContainer').on('click', '.sort-link', function (e) {
        e.preventDefault();
        e.stopPropagation();
        currentSort = $(this).data('sort');
        currentDirection = $(this).data('direction');
        fetchAnggaran();
    });

    // Pagination
    $('#tableContainer').on('click', '.ajax-pagination a', function (e) {
        e.preventDefault();
        e.stopPropagation();
        fetchAnggaran($(this).attr('href'));
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
            showToast('Nama anggaran harus diisi!', 'danger');
            return false;
        }
        if (!data.persentase_anggaran) {
            showToast('Persentase harus diisi!', 'danger');
            return false;
        }
        if (!data.id_pengeluaran || data.id_pengeluaran.length === 0) {
            showToast('Pilih setidaknya satu jenis pengeluaran!', 'danger');
            return false;
        }
        return true;
    }

    function resetForm() {
        $('#nama_anggaran').val('');
        $('#persentase_anggaran').val('');
        if (tomSelectInstance) tomSelectInstance.clear();
        $('#anggaranModal').removeData('id');
        $('#anggaranModalLabel').text('Tambah Anggaran Baru');
        $('.tombol-simpan-anggaran').html('Simpan').prop('disabled', false);
    }

    function saveBudget(isUpdate = false, id = null) {
        const data = getFormData();
        if (!validateForm(data)) return;

        const url = isUpdate ? `${baseUrl}/${id}` : baseUrl;
        const type = isUpdate ? 'PUT' : 'POST';

        $('.tombol-simpan-anggaran').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

        $.ajax({
            url: url,
            type: type,
            data: data,
            success: function (response) {
                showToast(response.message || 'Data berhasil disimpan', 'success');
                $('#anggaranModal').modal('hide');
                fetchAnggaran();
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const firstKey = Object.keys(errors)[0];
                    showToast(errors[firstKey][0], 'danger');
                } else {
                    showToast('Gagal menyimpan data.', 'danger');
                }
            },
            complete: function () {
                $('.tombol-simpan-anggaran').prop('disabled', false).html('Simpan');
            }
        });
    }

    // Event Listeners
    $('body').on('click', '.tombol-tambah-anggaran', function (e) {
        e.preventDefault();
        resetForm();
        $('#anggaranModal').modal('show');
    });

    $('#tableContainer').on('click', '.tombol-edit-anggaran', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');

        $.get(`${baseUrl}/${id}/edit`, function (res) {
            const anggaran = res.result;

            $('#anggaranModalLabel').text('Edit Anggaran');
            $('.tombol-simpan-anggaran').html('Perbarui');

            $('#nama_anggaran').val(anggaran.nama_anggaran);
            $('#persentase_anggaran').val(anggaran.persentase_anggaran);

            if (tomSelectInstance) {
                tomSelectInstance.clear();
                let selectedIds = [];
                if (Array.isArray(anggaran.id_pengeluaran)) {
                    selectedIds = anggaran.id_pengeluaran.map(id => String(id));
                } else if (typeof anggaran.id_pengeluaran === 'string') {
                    try {
                        const parsed = JSON.parse(anggaran.id_pengeluaran);
                        selectedIds = Array.isArray(parsed) ? parsed.map(id => String(id)) : [];
                    } catch (e) {
                        selectedIds = [];
                    }
                }
                tomSelectInstance.setValue(selectedIds);
            }

            $('#anggaranModal').data('id', id);
            $('#anggaranModal').modal('show');
        }).fail(function () {
            showToast('Gagal mengambil data anggaran.', 'danger');
        });
    });

    $(document).on('click', '.tombol-simpan-anggaran', function (e) {
        e.preventDefault();
        const id = $('#anggaranModal').data('id');
        saveBudget(!!id, id);
    });

    $('#anggaranModal').on('hidden.bs.modal', resetForm);

    $('#tableContainer').on('click', '.tombol-del-anggaran', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');

        window.confirmAction({
            title: 'Are you sure?',
            text: 'Deleted data cannot be recovered!',
            onConfirm: async () => {
                try {
                    await $.ajax({
                        url: `${baseUrl}/${id}`,
                        type: 'DELETE'
                    });
                    showToast('Data processed successfully', 'success');
                    fetchAnggaran();
                } catch (e) {
                    showToast('Failed to delete data', 'danger');
                }
            }
        });
    });

    // Bulk Delete
    $(document).on('change', '#checkAll', function () {
        $('.check-item').prop('checked', $(this).is(':checked'));
        updateBulkButton();
    });

    $(document).on('change', '.check-item', function () {
        const all = $('.check-item').length;
        const checked = $('.check-item:checked').length;
        $('#checkAll').prop('checked', all === checked);
        $('#checkAll').prop('indeterminate', checked > 0 && checked < all);
        updateBulkButton();
    });

    function updateBulkButton() {
        const count = $('.check-item:checked').length;
        $('#countSelected').text(count);
        if (count > 0) {
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

        window.confirmAction({
            title: `Delete ${ids.length} budgets?`,
            text: 'Deleted data cannot be recovered!',
            onConfirm: async () => {
                try {
                    const response = await $.ajax({
                        url: `${baseUrl}/bulk-delete`,
                        type: 'DELETE',
                        data: { ids: ids }
                    });
                    showToast(response.message || 'Data processed successfully', 'success');
                    fetchAnggaran();
                    $('#checkAll').prop('checked', false);
                } catch (e) {
                    showToast('Failed to delete selected data.', 'danger');
                }
            }
        });
    });
});
