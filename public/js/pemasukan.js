$(document).ready(function () {
    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let debounceTimer;
    const searchInput = $('#searchPemasukan');
    const tableContainer = $('#pemasukan-table-container');

    // Main Fetch Function
    function fetchPemasukan(url = window.location.href) {
        const urlObj = new URL(url, window.location.origin);

        const searchQuery = searchInput.val();
        if (searchQuery) urlObj.searchParams.set('search', searchQuery);

        tableContainer.css('opacity', '0.5');

        fetch(urlObj.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.json())
            .then(data => {
                tableContainer.css('opacity', '1');
                tableContainer.html(data.html);
                initBulkDelete();
            })
            .catch(error => {
                console.error('Error:', error);
                tableContainer.css('opacity', '1');
            });
    }

    // Search Input
    searchInput.on('keyup', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchPemasukan();
        }, 500);
    });

    // Pagination & Sorting Delegation
    tableContainer.on('click', '.pagination a, .sort-link', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const url = $(this).attr('href');
        if (url) fetchPemasukan(url);
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

    // Submit Form
    function simpanPemasukan(id = '') {
        const url = id ? `/pemasukan/${id}` : '/pemasukan';
        const method = id ? 'PUT' : 'POST';
        const nama = $('#nama').val().trim();

        if (nama === '') {
            showToast('Nama kategori harus diisi!', 'danger');
            return;
        }

        $('.tombol-simpan-pemasukan').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sedang memproses...');

        $.ajax({
            url,
            type: method,
            data: { nama },
            success: (response) => {
                showToast(response.message || 'Data berhasil disimpan', 'success');
                $('#pemasukanModal').modal('hide');
                fetchPemasukan(); // Reload table
            },
            error: (xhr) => {
                const msg = xhr.responseJSON?.message || 'Gagal menyimpan data';
                showToast(msg, 'danger');
            },
            complete: () => {
                $('.tombol-simpan-pemasukan').prop('disabled', false).html('Simpan');
            }
        });
    }

    // Tambah Event
    $('body').on('click', '.tombol-tambah-pemasukan', function (e) {
        e.preventDefault();
        $('#nama').val('');
        $('#pemasukanModal').modal('show');

        $('#pemasukanModal .modal-title').text('Tambah Kategori Income');
        $('#pemasukanModal').off('click', '.tombol-simpan-pemasukan')
            .on('click', '.tombol-simpan-pemasukan', () => simpanPemasukan());
    });

    // Edit Event
    tableContainer.on('click', '.tombol-edit-pemasukan', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');

        $.get(`/pemasukan/${id}/edit`, function (response) {
            $('#nama').val(response.result.nama);
            $('#pemasukanModal').modal('show');
            $('#pemasukanModal .modal-title').text('Edit Kategori Income');

            $('#pemasukanModal').off('click', '.tombol-simpan-pemasukan')
                .on('click', '.tombol-simpan-pemasukan', () => simpanPemasukan(id));
        });
    });

    // Hapus Single Event
    tableContainer.on('click', '.tombol-del-pemasukan', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/pemasukan/${id}`,
                    type: 'DELETE',
                    success: () => {
                        showToast('Data berhasil dihapus', 'success');
                        fetchPemasukan();
                    },
                    error: () => {
                        showToast('Gagal menghapus, data mungkin sedang digunakan', 'danger');
                    }
                });
            }
        });
    });

    // ----------------------------------------------------------------
    // BULK DELETE LOGIC
    // ----------------------------------------------------------------
    function initBulkDelete() {
        const checkAll = $('#checkAll');
        const btnBulkDelete = $('#btnBulkDelete');
        const countSelected = $('#countSelected');

        function updateUI() {
            const checked = $('.check-item:checked');
            const count = checked.length;
            countSelected.text(count);
            if (count > 0) btnBulkDelete.removeClass('d-none');
            else btnBulkDelete.addClass('d-none');

            const allItems = $('.check-item');
            if (checkAll.length && allItems.length) {
                checkAll.prop('checked', checked.length === allItems.length && allItems.length > 0);
                checkAll.prop('indeterminate', checked.length > 0 && checked.length < allItems.length);
            }
        }

        // Use standard event listeners (non-delegated here since initBulkDelete is called after HTML update)
        checkAll.off('change').on('change', function () {
            $('.check-item').prop('checked', this.checked);
            updateUI();
        });

        $('.check-item').off('change').on('change', function () {
            updateUI();
        });

        // Initialize UI state
        updateUI();
    }

    // Bulk Delete Action Event
    $('#btnBulkDelete').on('click', function () {
        const ids = $('.check-item:checked').map(function () { return $(this).val(); }).get();
        if (!ids.length) return;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Anda akan menghapus ${ids.length} kategori income!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $(this);
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                $.ajax({
                    url: "/pemasukan/bulk-delete",
                    type: 'DELETE',
                    data: { ids: ids },
                    success: function (response) {
                        showToast(response.message || 'Berhasil dihapus', 'success');
                        fetchPemasukan();
                        btn.addClass('d-none').prop('disabled', false).html(originalHtml);
                    },
                    error: function () {
                        showToast('Gagal menghapus kategori yang dipilih.', 'danger');
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    });

    // Initial load
    initBulkDelete();
});
