$(document).ready(function () {
    // Initialize DataTable
    var table = $('#goalsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/tujuan-keuangan',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            {
                data: 'nama_target',
                name: 'nama_target',
                className: 'goal-name-cell',
                render: function (data) {
                    return `<span title="${data}">${data}</span>`;
                }
            },
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
                    if (data === 'High') badge = 'bg-danger';
                    if (data === 'Medium') badge = 'bg-warning text-dark';
                    if (data === 'Low') badge = 'bg-info text-dark';
                    return `<span class="badge ${badge}">${data}</span>`;
                }
            },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
        ]
    });

    // Handle interactive recommendation in Add modal
    $('#input_target, #input_deadline').on('input change', function () {
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
                    recText = "Deadline must be in the future.";
                } else {
                    const perDay = target / days;
                    recText = `You need to save <strong>Rp ${new Intl.NumberFormat('id-ID').format(Math.ceil(perDay))}/day</strong> to reach this goal.`;
                }
            } else {
                const perMonth = target / months;
                recText = `You need to save <strong>Rp ${new Intl.NumberFormat('id-ID').format(Math.ceil(perMonth))}/month</strong> to reach this goal.`;
            }

            $('#recommendationText').html(recText);
            $('#recommendationAlert').fadeIn();
        } else {
            $('#recommendationAlert').fadeOut();
        }
    });

    // Simulation logic
    const slider = $('#simSlider');
    slider.on('input', function () {
        const monthly = parseInt($(this).val());
        $('#simMonthlyValue').text('Rp ' + new Intl.NumberFormat('id-ID').format(monthly));

        const target = parseFloat(slider.data('target'));
        const current = parseFloat(slider.data('collected'));
        const remaining = target - current;

        if (remaining <= 0) {
            $('#simResultDate').text('Already Reached!');
            $('#simMonthsLeft').text('');
            return;
        }

        const monthsNeeded = Math.ceil(remaining / monthly);
        const finishDate = moment().add(monthsNeeded, 'months');

        $('#simResultDate').text(finishDate.format('MMMM YYYY'));
        $('#simMonthsLeft').text(`In approximately ${monthsNeeded} month(s) from now.`);
    });
});

function updateProgress(id, name) {
    $('#goalNameProgress').text(name);
    $('#formUpdateProgress').attr('action', `/tujuan-keuangan/${id}/progress`);
    $('#formUpdateProgress')[0].reset(); // Reset form field
    $('#modalProgress').modal('show');
}

function viewHistory(id, name, target) {
    $('#historyGoalName').text(name);
    $('#historyGoalTarget').html(`Target: Rp ${new Intl.NumberFormat('id-ID').format(target)}`);

    $('#historyList').html('<tr><td colspan="4" class="text-center">Loading history...</td></tr>');
    $('#modalHistory').modal('show');

    $.get(`/tujuan-keuangan/${id}/history`, function (logs) {
        let html = '';
        if (logs.length === 0) {
            html = '<tr><td colspan="4" class="text-center">No progress history found.</td></tr>';
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

function deleteHistoryLog(logId) {
    Swal.fire({
        title: 'Delete this entry?',
        text: "This will also reduce your collected amount progress.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
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
                        Swal.fire('Deleted!', 'History entry removed and progress adjusted.', 'success');
                        $('#modalHistory').modal('hide');
                        $('#goalsTable').DataTable().ajax.reload();
                    }
                }
            });
        }
    });
}

function simulateGoal(id, name, target, collected) {
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

function deleteGoal(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
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
                        Swal.fire('Deleted!', 'Goal has been deleted.', 'success');
                        $('#goalsTable').DataTable().ajax.reload();
                    }
                }
            });
        }
    });
}
