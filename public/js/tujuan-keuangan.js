let isTujuanKeuanganInitialized = false;
window.__tujuanKeuanganSelectedGoalIds = window.__tujuanKeuanganSelectedGoalIds || new Set();
const TUJUAN_KEUANGAN_FREEZE_STORAGE_KEY = 'tujuanKeuanganFreezeCols';

window.initTujuanKeuangan = function () {
    if (!$('#goalsTable').length) return;

    if (isTujuanKeuanganInitialized) return;
    isTujuanKeuanganInitialized = true;
    setTimeout(() => { isTujuanKeuanganInitialized = false; }, 500);


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
        paging: true,
        responsive: true,
        lengthChange: true,
        processing: true,
        serverSide: true,
        dom: '<"dt-top-bar"lf>t<"dt-bottom-bar"ip>',
        order: [],
        columnDefs: [
            {
                targets: 0, // bulk checkbox column
                orderable: false,
                searchable: false,
            },
        ],
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
            {
                data: 'id_tujuan_keuangan',
                name: 'id_tujuan_keuangan',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data) {
                    const id = String(data);
                    return `
                        <input
                            type="checkbox"
                            class="form-check-input goal-select"
                            value="${id}"
                            aria-label="Select goal ${id}"
                        >
                    `;
                }
            },
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

    const selectedIds = window.__tujuanKeuanganSelectedGoalIds;

    function getFreezeableColumns() {
        // DataTables column indexes (0-based)
        return [
            { index: 0, label: 'Checklist' },
            { index: 1, label: 'No' },
            { index: 2, label: 'Goal Name' },
            { index: 3, label: 'Category' },
            { index: 4, label: 'Target' },
            { index: 5, label: 'Collected' },
            { index: 6, label: 'Progress' },
            { index: 7, label: 'Remaining Time' },
            { index: 8, label: 'Rec. Savings/Mo' },
            { index: 9, label: 'Priority' },
        ];
    }

    function readFrozenColumns() {
        try {
            const raw = localStorage.getItem(TUJUAN_KEUANGAN_FREEZE_STORAGE_KEY);
            const parsed = raw ? JSON.parse(raw) : [];
            if (!Array.isArray(parsed)) return [];
            return parsed.map(Number).filter((n) => Number.isFinite(n));
        } catch {
            return [];
        }
    }

    function writeFrozenColumns(indices) {
        const uniqueSorted = Array.from(new Set(indices.map(Number).filter((n) => Number.isFinite(n)))).sort((a, b) => a - b);
        localStorage.setItem(TUJUAN_KEUANGAN_FREEZE_STORAGE_KEY, JSON.stringify(uniqueSorted));
    }

    function injectFreezeUi() {
        const wrapper = document.getElementById('goalsTable_wrapper');
        if (!wrapper) return false;

        // Preferred placement is already rendered in Blade.
        // If the Blade dropdown exists, just wire it up.
        const preferredMount = document.getElementById('freezeColumnsContainer');
        const existingToggle = document.getElementById('tkFreezeDropdown');
        const existingMenu = preferredMount?.querySelector('.dropdown-menu');
        const existingList = preferredMount?.querySelector('.tk-freeze-list');
        if (preferredMount && existingToggle && existingMenu && existingList) {
            wireFreezeUi(preferredMount, existingToggle, existingMenu, existingList);
            return true;
        }

        if (wrapper.querySelector('[data-tk-freeze-ui="1"]')) return true;

        let topBar = wrapper.querySelector('.dt-top-bar');
        if (!topBar) {
            // Fallback: create a top bar and move length + filter into it
            const length = wrapper.querySelector('.dataTables_length');
            const filter = wrapper.querySelector('.dataTables_filter');
            if (!length && !filter) return false;

            topBar = document.createElement('div');
            topBar.className = 'dt-top-bar';

            const insertBeforeNode =
                wrapper.querySelector('.dataTables_scroll') ||
                wrapper.querySelector('table') ||
                wrapper.firstChild;

            wrapper.insertBefore(topBar, insertBeforeNode);

            if (length) topBar.appendChild(length);
            if (filter) topBar.appendChild(filter);
        }

        const container = document.createElement('div');
        container.setAttribute('data-tk-freeze-ui', '1');
        container.className = 'ms-2 d-flex align-items-center';

        const dropdownId = `tkFreezeDropdown-${Date.now()}`;
        container.innerHTML = `
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="${dropdownId}" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    Freeze Columns
                </button>
                <div class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="${dropdownId}" style="min-width: 240px;">
                    <div class="small text-muted mb-2">Pilih kolom yang mau di-freeze</div>
                    <div class="tk-freeze-list" style="max-height: 220px; overflow:auto;"></div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-light btn-sm w-50" data-tk-freeze-clear="1">Clear</button>
                        <button type="button" class="btn btn-primary btn-sm w-50" data-tk-freeze-apply="1">Apply</button>
                    </div>
                </div>
            </div>
        `;

        const filterInTopBar = topBar.querySelector('.dataTables_filter');
        if (filterInTopBar && filterInTopBar.parentElement === topBar) topBar.insertBefore(container, filterInTopBar.nextSibling);
        else topBar.appendChild(container);

        const toggleEl = document.getElementById(dropdownId);
        const menuEl = container.querySelector('.dropdown-menu');
        const listEl = container.querySelector('.tk-freeze-list');
        wireFreezeUi(container, toggleEl, menuEl, listEl);

        return true;
    }

    function wireFreezeUi(rootEl, toggleEl, menuEl, listEl) {
        if (!rootEl || !toggleEl || !menuEl || !listEl) return;
        if (rootEl.getAttribute('data-tk-freeze-wired') === '1') return;
        rootEl.setAttribute('data-tk-freeze-wired', '1');

        const columns = getFreezeableColumns();
        const current = new Set(readFrozenColumns());
        listEl.innerHTML = columns.map((c) => {
            const checked = current.has(c.index) ? 'checked' : '';
            return `
                <label class="dropdown-item d-flex align-items-center gap-2 py-1 px-1 m-0" style="cursor:pointer;">
                    <input class="form-check-input m-0" type="checkbox" value="${c.index}" ${checked}>
                    <span class="small">${c.label}</span>
                </label>
            `;
        }).join('');

        // Prevent dropdown from closing while interacting inside
        menuEl.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        rootEl.querySelector('[data-tk-freeze-clear="1"]')?.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            rootEl.querySelectorAll('.tk-freeze-list input[type="checkbox"]').forEach((el) => { el.checked = false; });
        });

        rootEl.querySelector('[data-tk-freeze-apply="1"]')?.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const selected = Array.from(rootEl.querySelectorAll('.tk-freeze-list input[type="checkbox"]:checked'))
                .map((el) => Number(el.value))
                .filter((n) => Number.isFinite(n));
            writeFrozenColumns(selected);
            applyFrozenColumnsFromStorage(true);

            const dropdownInstance = bootstrap.Dropdown.getOrCreateInstance(toggleEl);
            dropdownInstance.hide();
        });
    }

    function applyFrozenColumnsFromStorage(forceRecalc = false) {
        const indices = readFrozenColumns();
        applyFrozenColumns(indices, forceRecalc);
    }

    function applyFrozenColumns(frozenIndices, forceRecalc) {
        const wrapper = document.getElementById('goalsTable_wrapper');
        if (!wrapper) return;

        const headTable = wrapper.querySelector('.dataTables_scrollHead table');
        const bodyTable = wrapper.querySelector('.dataTables_scrollBody table');
        if (!headTable || !bodyTable) return;

        const headRow = headTable.tHead?.rows?.[0];
        if (!headRow) return;

        const headCells = Array.from(headRow.cells);

        // Clear old sticky classes/styles
        [headTable, bodyTable].forEach((tbl) => {
            tbl.querySelectorAll('.tk-sticky, .tk-sticky-last').forEach((cell) => {
                cell.classList.remove('tk-sticky', 'tk-sticky-last');
                cell.style.removeProperty('--tk-left');
            });
        });

        const frozen = Array.from(new Set(frozenIndices.map(Number).filter((n) => Number.isFinite(n)))).sort((a, b) => a - b);
        if (frozen.length === 0) return;

        const widths = headCells.map((cell) => cell.getBoundingClientRect().width);

        const apply = () => {
            let left = 0;
            frozen.forEach((colIndex, i) => {
                const isLast = i === frozen.length - 1;
                const headCell = headCells[colIndex];
                if (headCell) {
                    headCell.classList.add('tk-sticky');
                    if (isLast) headCell.classList.add('tk-sticky-last');
                    headCell.style.setProperty('--tk-left', `${left}px`);
                }

                bodyTable.querySelectorAll('tbody tr').forEach((tr) => {
                    const cell = tr.children[colIndex];
                    if (!cell) return;
                    cell.classList.add('tk-sticky');
                    if (isLast) cell.classList.add('tk-sticky-last');
                    cell.style.setProperty('--tk-left', `${left}px`);
                });

                left += widths[colIndex] || 0;
            });
        };

        if (forceRecalc) requestAnimationFrame(() => requestAnimationFrame(apply));
        else requestAnimationFrame(apply);
    }

    // Build freeze UI + apply saved state once wrapper exists (retry a few times for slow renders)
    (function scheduleFreezeUiInit() {
        let attempts = 0;
        const maxAttempts = 20; // ~2s
        const tick = () => {
            attempts += 1;
            const injected = injectFreezeUi();
            if (injected) {
                applyFrozenColumnsFromStorage(true);
                return;
            }
            if (attempts < maxAttempts) setTimeout(tick, 100);
        };
        setTimeout(tick, 0);
    })();

    function updateBulkUI() {
        const count = selectedIds.size;
        const $count = $('#bulkSelectedCount');
        const $btn = $('#bulkDeleteGoalsBtn');
        const selectedLabel = $count.data('selected-label') || 'selected';

        if (count > 0) {
            $count.text(`${count} ${selectedLabel}`).show();
            $btn.prop('disabled', false);
        } else {
            $count.hide();
            $btn.prop('disabled', true);
        }
    }

    function syncSelectAllState() {
        const $selectAll = $('#selectAllGoals');
        if (!$selectAll.length) return;

        if (!$.fn.DataTable.isDataTable('#goalsTable')) {
            $selectAll.prop('checked', false).prop('indeterminate', false);
            return;
        }

        const nodes = table.rows({ page: 'current' }).nodes().toArray();
        const checkboxes = nodes.map(n => $(n).find('input.goal-select')[0]).filter(Boolean);

        if (checkboxes.length === 0) {
            $selectAll.prop('checked', false).prop('indeterminate', false);
            return;
        }

        const checkedCount = checkboxes.filter(cb => cb.checked).length;
        $selectAll.prop('checked', checkedCount === checkboxes.length);
        $selectAll.prop('indeterminate', checkedCount > 0 && checkedCount < checkboxes.length);
    }

    table.on('draw', function () {
        // Re-apply checked state for current page based on global selection
        $('#goalsTable tbody input.goal-select').each(function () {
            const id = String($(this).val());
            $(this).prop('checked', selectedIds.has(id));
        });
        syncSelectAllState();
        updateBulkUI();
        applyFrozenColumnsFromStorage();
    });

    // Bulk selection handlers (delegated)
    $(document)
        .off('change.tujuan_keuangan', '#selectAllGoals')
        .on('change.tujuan_keuangan', '#selectAllGoals', function () {
            const checked = $(this).is(':checked');
            $('#goalsTable tbody input.goal-select').each(function () {
                const id = String($(this).val());
                $(this).prop('checked', checked);
                if (checked) selectedIds.add(id);
                else selectedIds.delete(id);
            });
            syncSelectAllState();
            updateBulkUI();
        });

    $(document)
        .off('change.tujuan_keuangan', '#goalsTable tbody input.goal-select')
        .on('change.tujuan_keuangan', '#goalsTable tbody input.goal-select', function () {
            const id = String($(this).val());
            if ($(this).is(':checked')) selectedIds.add(id);
            else selectedIds.delete(id);
            syncSelectAllState();
            updateBulkUI();
        });

    $(document)
        .off('click.tujuan_keuangan', '#bulkDeleteGoalsBtn')
        .on('click.tujuan_keuangan', '#bulkDeleteGoalsBtn', function () {
            if (selectedIds.size === 0) return;

            const ids = Array.from(selectedIds);
            window.confirmAction({
                title: 'Delete selected goals?',
                text: `You are about to delete ${ids.length} goal(s). This cannot be undone!`,
                onConfirm: async () => {
                    try {
                        const response = await $.ajax({
                            url: '/tujuan-keuangan/bulk-delete',
                            type: 'DELETE',
                            data: {
                                ids: ids,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        if (response.success) {
                            selectedIds.clear();
                            $('#selectAllGoals').prop('checked', false).prop('indeterminate', false);
                            updateBulkUI();
                            showToast(response.message || 'Goals deleted successfully.', 'success');
                            if ($.fn.DataTable.isDataTable('#goalsTable')) {
                                $('#goalsTable').DataTable().ajax.reload(null, false);
                            }
                        } else {
                            showToast(response.message || 'Failed to delete goals.', 'danger');
                        }
                    } catch (e) {
                        showToast('Failed to delete goals.', 'danger');
                    }
                }
            });
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
    window.confirmAction({
        title: 'Delete this entry?',
        text: 'This will also reduce the progress of collected amounts.',
        onConfirm: async () => {
            try {
                const response = await $.ajax({
                    url: `/tujuan-keuangan/log/${logId}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                });
                if (response.success) {
                    showToast('Entry deleted successfully.', 'success');
                    $('#modalHistory').modal('hide');
                    if ($.fn.DataTable.isDataTable('#goalsTable')) {
                        $('#goalsTable').DataTable().ajax.reload();
                    }
                }
            } catch (e) {
                showToast('Failed to delete history entry.', 'danger');
            }
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
    window.confirmAction({
        title: 'Are you sure?',
        text: 'Deleted data cannot be recovered!',
        onConfirm: async () => {
            try {
                const response = await $.ajax({
                    url: `/tujuan-keuangan/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                });
                if (response.success) {
                    showToast('Goal deleted successfully.', 'success');
                    if ($.fn.DataTable.isDataTable('#goalsTable')) {
                        $('#goalsTable').DataTable().ajax.reload();
                    }
                }
            } catch (e) {
                showToast('Failed to delete goal.', 'danger');
            }
        }
    });
}

// Initial initialization
$(document).ready(function () {
    window.initTujuanKeuangan();
});

document.addEventListener('livewire:navigated', function () {
    window.initTujuanKeuangan();
});
