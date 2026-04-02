window.initTujuanKeuangan = function () {
    if (!$('#goalsTable').length) return;

    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Check if DataTable is already initialized to avoid re-init error
    if ($.fn.DataTable.isDataTable('#goalsTable')) {
        $('#goalsTable').DataTable().destroy();
    }

    // Initialize DataTable
    var table = $('#goalsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/tujuan-keuangan',
            data: function (d) {
                d.filter_kategori = $('#filter_kategori').val();
                d.filter_prioritas = $('#filter_prioritas').val();
            }
        },
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        scrollX: true,
        autoWidth: false,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama_target', name: 'nama_target' },
            { data: 'kategori', name: 'kategori' },
            {
                data: 'nominal_target',
                name: 'nominal_target',
                render: function (data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: 'nominal_terkumpul',
                name: 'nominal_terkumpul',
                render: function (data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: 'progress',
                name: 'progress',
                className: 'text-center',
                render: function (data) {
                    let color = 'bg-primary';
                    if (data >= 100) color = 'bg-success';
                    else if (data > 75) color = 'bg-info';
                    else if (data < 25) color = 'bg-danger';

                    return `
                        <div class="progress" style="height: 10px; width: 100px; margin: auto;">
                            <div class="progress-bar ${color}" role="progressbar" style="width: ${data}%" aria-valuenow="${data}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">${data}%</small>
                    `;
                }
            },
            { data: 'sisa_waktu', name: 'sisa_waktu' },
            {
                data: 'rekomendasi',
                name: 'rekomendasi',
                render: function (data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.ceil(data)) + '/mo';
                }
            },
            {
                data: 'prioritas',
                name: 'prioritas',
                className: 'text-center',
                render: function (data) {
                    let badge = 'bg-secondary';
                    let label = data;
                    if (data === 'High') {
                        badge = 'bg-danger';
                        label = 'Tinggi';
                    } else if (data === 'Medium') {
                        badge = 'bg-warning text-dark';
                        label = 'Sedang';
                    } else if (data === 'Low') {
                        badge = 'bg-info text-dark';
                        label = 'Rendah';
                    }
                    return `<span class="badge ${badge}">${label}</span>`;
                }
            },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
        ]
    });

    // Handle Filters
    $('#filter_kategori, #filter_prioritas').off('change').on('change', function () {
        table.ajax.reload();
    });

    // Handle interactive recommendation in Add modal
    $('#input_target, #input_deadline').off('input change').on('input change', function () {
        const target = parseFloat($('#input_target').val());
        const deadline = $('#input_deadline').val();

        if (target > 0 && deadline) {
            const today = moment();
            const end = moment(deadline);
            const months = end.diff(today, 'months');

            let recText = "";
            if (months <= 0) {
                const days = end.diff(today, 'days');
                if (days <= 0) {
                    recText = "Tenggat waktu harus di masa depan.";
                } else {
                    const perDay = target / days;
                    recText = `Anda perlu menabung <strong>Rp ${new Intl.NumberFormat('id-ID').format(Math.ceil(perDay))}/hari</strong> untuk mencapai tujuan ini.`;
                }
            } else {
                const perMonth = target / months;
                recText = `Anda perlu menabung <strong>Rp ${new Intl.NumberFormat('id-ID').format(Math.ceil(perMonth))}/bulan</strong> untuk mencapai tujuan ini.`;
            }

            $('#recommendationText').html(recText);
            $('#recommendationAlert').fadeIn();
        } else {
            $('#recommendationAlert').fadeOut();
        }
    });

    // Simulation logic
    const slider = $('#simSlider');
    slider.off('input').on('input', function () {
        const monthly = parseInt($(this).val());
        $('#simMonthlyValue').text('Rp ' + new Intl.NumberFormat('id-ID').format(monthly));

        const target = parseFloat(slider.data('target'));
        const current = parseFloat(slider.data('collected'));
        const remaining = target - current;

        if (remaining <= 0) {
            $('#simResultDate').text('Sudah Tercapai!');
            $('#simMonthsLeft').text('');
            return;
        }

        const monthsNeeded = Math.ceil(remaining / monthly);
        const finishDate = moment().add(monthsNeeded, 'months');

        $('#simResultDate').text(finishDate.locale('id').format('MMMM YYYY'));
        $('#simMonthsLeft').text(`Sekitar ${monthsNeeded} bulan lagi dari sekarang.`);
    });
}

// Initial initialization
$(document).ready(function () {
    window.initTujuanKeuangan();
});

// Global functions for inline onclick handlers
window.updateProgress = function (id, name) {
    $('#goalNameProgress').text(name);
    $('#formUpdateProgress').attr('action', `/tujuan-keuangan/${id}/progress`);
    if ($('#formUpdateProgress')[0]) $('#formUpdateProgress')[0].reset();
    $('#modalProgress').modal('show');
}

window.viewHistory = function (id, name, target) {
    $('#historyGoalName').text(name);
    $('#historyGoalTarget').html(`Target: Rp ${new Intl.NumberFormat('id-ID').format(target)}`);

    $('#historyList').html('<tr><td colspan="4" class="text-center">Memuat riwayat...</td></tr>');
    $('#modalHistory').modal('show');

    $.get(`/tujuan-keuangan/${id}/history`, function (logs) {
        let html = '';
        if (logs.length === 0) {
            html = '<tr><td colspan="4" class="text-center">Tidak ada riwayat progres ditemukan.</td></tr>';
        } else {
            logs.forEach(log => {
                const date = moment(log.created_at).format('DD MMM YYYY, HH:mm');
                const amount = 'Rp ' + new Intl.NumberFormat('id-ID').format(log.nominal_tambah);
                html += `
                    <tr>
                        <td>${date}</td>
                        <td class="text-success fw-bold">+ ${amount}</td>
                        <td>${log.keterangan || '-'}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteHistoryLog(${log.id_log})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        $('#historyList').html(html);
    });
}

window.deleteHistoryLog = function (logId) {
    Swal.fire({
        title: 'Hapus entri ini?',
        text: "Ini juga akan mengurangi progres jumlah yang terkumpul.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/tujuan-keuangan/log/${logId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', 'Entri riwayat dihapus dan progres disesuaikan.', 'success');
                        $('#modalHistory').modal('hide');
                        if ($.fn.DataTable.isDataTable('#goalsTable')) {
                            $('#goalsTable').DataTable().ajax.reload();
                        }
                    }
                }
            });
        }
    });
}

window.simulateGoal = function (id, name, target, collected) {
    $('#simGoalName').text(name);
    $('#simTargetInfo').text(`Target: Rp ${new Intl.NumberFormat('id-ID').format(target)} (Remaining: Rp ${new Intl.NumberFormat('id-ID').format(target - collected)})`);

    // Set data to slider
    const slider = $('#simSlider');
    slider.data('target', target);
    slider.data('collected', collected);

    // Initial calculation
    slider.trigger('input');

    $('#modalSimulate').modal('show');
}

window.deleteGoal = function (id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda tidak akan dapat mengembalikan ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/tujuan-keuangan/${id}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', 'Tujuan telah dihapus.', 'success');
                        if ($.fn.DataTable.isDataTable('#goalsTable')) {
                            $('#goalsTable').DataTable().ajax.reload();
                        }
                    }
                }
            });
        }
    });
}
