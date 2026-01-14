document.addEventListener("DOMContentLoaded", function () {
    // ==== THEME HANDLER ====
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

        fetch(updateSkinUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                skin: mode
            })
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success) alert("Gagal menyimpan tema.");
            })
            .catch(err => console.error("Gagal update tema:", err));
    }

    // Eksekusi awal tema
    applyTheme(skin);
    highlightActiveSkin(skin);
    window.setTheme = setTheme;

    // Fungsi Toast
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
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }

    $(document).ready(function () {
        // Inisialisasi TomSelect
        const tomSelect = new TomSelect('#id_pengeluaran', {
            plugins: ['remove_button'],
            create: false,
            maxItems: null,
            hideSelected: true,
            closeAfterSelect: false
        });
    });

    // DataTable
    const anggaranTable = $('#anggaranTable').DataTable({
        paging: true,
        responsive: true,
        serverSide: true,
        processing: true,
        lengthChange: true,
        autoWidth: false,
        ajax: {
            url: '/anggaran',
            type: 'GET',
            dataSrc: function (json) {
                $('#totalPersentase').text(json.totalPersentase.toLocaleString('id-ID') + '%');
                if (json.exceedMessage) {
                    $('#exceedMessage').text(json.exceedMessage).show();
                } else {
                    $('#exceedMessage').hide();
                }
                return json.data;
            }
        },
        columns: [{
            data: 'DT_RowIndex',
            className: 'text-center',
            orderable: false,
            searchable: false
        },
        {
            data: 'nama_anggaran',
            className: 'text-center',
            render: d => d || '-'
        },
        {
            data: 'persentase_anggaran',
            className: 'text-center'
        },
        {
            data: 'list_pengeluaran',
            name: 'list_pengeluaran',
            className: 'text-left',
            defaultContent: '-',
            render: function (data, type, row) {
                if (type !== "display" || !Array.isArray(data) || data.length === 0) {
                    return "-";
                }

                const showLimit = 3;
                const hasMore = data.length > showLimit;
                const visible = data.slice(0, showLimit);
                const hidden = data.slice(showLimit);
                const tableId = `detail-table-${row.id_anggaran}`;

                let table = `
                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #dee2e6; font-size: 13px;" id="${tableId}">
                            <colgroup>
                                <col style="width: 40px;"> <!-- 🔹 kolom nomor dibuat tetap -->
                                <col style="width: auto;">
                            </colgroup>
                            <tbody>
                                ${visible.map((name, i) => `
                                    <tr>
                                        <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">${i + 1}</td>
                                        <td style="border: 1px solid #dee2e6; padding: 4px;">${name}</td>
                                    </tr>
                                `).join('')}
                    `;

                if (hasMore) {
                    table += hidden.map((name, i) => `
                            <tr class="hidden-row" style="display: none;">
                                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">${showLimit + i + 1}</td>
                                <td style="border: 1px solid #dee2e6; padding: 4px;">${name}</td>
                            </tr>
                        `).join('');
                }

                table += `
                            </tbody>
                        </table>
                    `;

                if (hasMore) {
                    table += `
                            <button type="button" class="btn btn-sm btn-link p-0 mt-1 toggle-btn" data-target="${tableId}">
                                More Details
                            </button>
                        `;
                }
                return table;
            }
        },
        {
            data: 'created_at',
            render: d => moment(d).format('YYYY-MM-DD HH:mm:ss'),
            className: 'text-center'
        },
        {
            data: 'updated_at',
            render: d => moment(d).format('YYYY-MM-DD HH:mm:ss'),
            className: 'text-center'
        },
        {
            data: 'aksi',
            className: 'text-center',
            orderable: false,
            searchable: false
        }
        ]
    });

    $(document).on('click', '.toggle-btn', function () {
        const tableId = $(this).data('target');
        const $table = $('#' + tableId);
        const $hiddenRows = $table.find('.hidden-row');

        if ($hiddenRows.is(':visible')) {
            $hiddenRows.hide();
            $(this).text('More Details');
        } else {
            $hiddenRows.show();
            $(this).text('Show Less');
        }
    });

    // Utility Functions
    function ambilFormDataAnggaran() {
        return {
            nama_anggaran: $('#nama_anggaran').val().trim(),
            persentase_anggaran: $('#persentase_anggaran').val().trim(),
            id_pengeluaran: $('#id_pengeluaran').val()
        };
    }

    function validasiFormAnggaran(data) {
        if (!data.nama_anggaran) {
            showToast('Nama anggaran harus diisi!', 'danger');
            return false;
        }
        if (!data.persentase_anggaran) {
            showToast('Persentase harus diisi!', 'danger');
            return false;
        }
        return true;
    }

    function resetFormAnggaran() {
        $('#nama_anggaran').val('');
        $('#persentase_anggaran').val('');
        $('#id_pengeluaran')[0].tomselect.clear();
        $('#anggaranModal').removeData('id');
        $('#anggaranModalLabel');
        $('.tombol-simpan-anggaran');
    }

    function spinnerButton() {
        return '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    }

    function resetTombolSimpanAnggaran() {
        $('.tombol-simpan-anggaran').prop('disabled', false).html('Simpan');
    }

    function onSuccessSimpanAnggaran() {
        showToast('Data saved successfully', 'success');
        $('#anggaranModal').modal('hide');
        $('#anggaranTable').DataTable().ajax.reload();
    }

    function simpanAnggaranBaru() {
        const data = ambilFormDataAnggaran();
        if (!validasiFormAnggaran(data)) return;

        $('.tombol-simpan-anggaran').prop('disabled', true).html(spinnerButton());

        $.post('/anggaran', data)
            .done(onSuccessSimpanAnggaran)
            .always(resetTombolSimpanAnggaran);
    }

    function updateAnggaran(id) {
        const data = ambilFormDataAnggaran();
        if (!validasiFormAnggaran(data)) return;

        $('.tombol-simpan-anggaran').prop('disabled', true).html(spinnerButton());

        $.ajax({
            url: '/anggaran/' + id,
            type: 'PUT',
            data,
            success: onSuccessSimpanAnggaran,
            complete: resetTombolSimpanAnggaran
        });
    }

    // Event Handlers
    $('body').on('click', '.tombol-tambah-anggaran', function (e) {
        e.preventDefault();
        $('#anggaranModal').modal('show');
    });

    $('body').on('click', '.tombol-edit-anggaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.get('/anggaran/' + id + '/edit', function (res) {
            const anggaran = res.result;

            $('#anggaranModal').modal('show');
            $('#anggaranModalLabel').text('Edit Anggaran');
            $('.tombol-simpan-anggaran').html('Memperbarui');

            $('#nama_anggaran').val(anggaran.nama_anggaran);
            $('#persentase_anggaran').val(anggaran.persentase_anggaran);

            const selectInstance = $('#id_pengeluaran')[0].tomselect;
            selectInstance.clear();

            // Tambahkan opsi id => nama
            Object.entries(anggaran.id_pengeluaran).forEach(([id, nama]) => {
                if (!selectInstance.options[id]) {
                    selectInstance.addOption({
                        value: id,
                        text: nama
                    });
                }
            });

            // Set value hanya id
            selectInstance.setValue(Object.keys(anggaran.id_pengeluaran));

            $('#anggaranModal').data('id', id);
        });
    });



    $('body').on('click', '.tombol-simpan-anggaran', function (e) {
        e.preventDefault();
        const id = $('#anggaranModal').data('id');
        id ? updateAnggaran(id) : simpanAnggaranBaru();
    });

    $('#anggaranModal').on('hidden.bs.modal', resetFormAnggaran);

    $('body').on('click', '.tombol-del-anggaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        Swal.fire({
            title: 'Apakah Anda yakin ingin menghapus data ini?',
            html: 'Data yang dihapus tidak dapat dipulihkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isDarkMode ? '#6f42c1' : '#012970',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            background: isDarkMode ? '#2c2c3c' : '#ffffff',
            color: isDarkMode ? '#ffffff' : '#000000',
            customClass: {
                popup: isDarkMode ? 'swal2-dark' : ''
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/anggaran/' + id,
                    type: 'DELETE',
                    success: function () {
                        showToast('Data berhasil dihapus', 'success');
                        $('#anggaranTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        showToast('Data gagal dihapus', 'danger');
                        $('#anggaranTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });

    // Setup CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
