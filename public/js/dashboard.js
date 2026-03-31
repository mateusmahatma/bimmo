document.addEventListener("DOMContentLoaded", function () {
    const csrfToken = window.csrfToken;

    // ==== DASHBOARD FILTER HANDLER (Global elements that aren't specific to Cash Flow) ====
    // Note: Cash Flow specific filtering is now in dashboard-cashflow.js

    // Expenses Bar & Table Initial Load
    const filterBulan = document.getElementById("filterBulan");
    const filterTahun = document.getElementById("filterTahun");

    function updateExpenseBar(month, year) {
        if (window.fetchDataAndRenderChart) window.fetchDataAndRenderChart(month, year);

        fetch(`${window.dashboardFilterUrl}?bulan=${month}&tahun=${year}`)
            .then(res => res.json())
            .then(data => {
                const tableContainer = document.getElementById("expenseBarTableContainer");
                const totalValue = document.getElementById("totalPengeluaranValue");
                if (tableContainer) tableContainer.innerHTML = data.expenseBar;
                if (totalValue) totalValue.textContent = "Rp " + data.totalPengeluaran;
            })
            .catch(err => console.error("Error updating expense bar:", err));
    }

    if (filterBulan && filterTahun) {
        const handleExpenseFilter = () => updateExpenseBar(filterBulan.value, filterTahun.value);
        filterBulan.addEventListener("change", handleExpenseFilter);
        filterTahun.addEventListener("change", handleExpenseFilter);
    }

    // ==== PERFORMANCE RATIO RADAR/BAR CHART ====
    const chartElement = document.getElementById("chartKompas");
    if (chartElement) {
        let chart;
        const rasio = parseFloat(chartElement.dataset.rasio);
        const rasio_inflasi = parseFloat(chartElement.dataset.rasioInflasi);
        const rasio_dana_darurat = parseFloat(chartElement.dataset.rasioDanaDarurat);
        const rasio_pengeluaran_pendapatan = parseFloat(chartElement.dataset.rasioPengeluaranPendapatan);

        const getTextColor = () => document.documentElement.getAttribute("data-bs-theme") === "dark" ? "#ffffff" : "#000000";

        const getColor = (val, target, inverse = false) => {
            if (inverse) return val >= target ? "var(--bs-success)" : "var(--bs-danger)";
            return val <= target ? "var(--bs-success)" : "var(--bs-danger)";
        };

        const options = {
            chart: { type: "bar", height: 300, toolbar: { show: false }, foreColor: getTextColor() },
            plotOptions: { bar: { horizontal: true, borderRadius: 8 } },
            series: [{
                name: "Value",
                data: [
                    { x: "Debt Ratio", y: rasio, fillColor: getColor(rasio, 20) },
                    { x: "Lifestyle Inflation", y: rasio_inflasi, fillColor: getColor(rasio_inflasi, 0) },
                    { x: "Emergency Fund", y: rasio_dana_darurat, fillColor: getColor(rasio_dana_darurat, 100, true) },
                    { x: "Exp/Inc Ratio", y: rasio_pengeluaran_pendapatan, fillColor: getColor(rasio_pengeluaran_pendapatan, 70) }
                ]
            }],
            xaxis: { labels: { formatter: v => v + '%' } },
            tooltip: { theme: document.documentElement.getAttribute("data-bs-theme") === 'dark' ? 'dark' : 'light' }
        };

        chart = new ApexCharts(chartElement, options);
        chart.render();

        document.addEventListener("themeChanged", () => {
            chart.updateOptions({ chart: { foreColor: getTextColor() } });
        });
    }

    // ==== TOGGLE NOMINAL VISIBILITY AJAX ====
    const toggleBtn = document.getElementById('toggleNominalBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', async () => {
            toggleBtn.disabled = true;
            try {
                const response = await fetch(toggleBtn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const res = await response.json();
                document.getElementById('summary-saldo').textContent = res.data.saldo;
                document.getElementById('summary-pemasukan').textContent = res.data.pemasukan;
                document.getElementById('summary-pengeluaran').textContent = res.data.pengeluaran;
                document.getElementById('summary-cicilan-besok').textContent = res.data.cicilan_besok;
                document.getElementById('summary-hari-ini').textContent = res.data.hari_ini;
                toggleBtn.innerHTML = res.show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            } catch (e) { console.error(e); }
            toggleBtn.disabled = false;
        });
    }

    // ==== NOTES MANAGEMENT AJAX (ENHANCED) ====
    const notesModal = document.getElementById('notesModal');
    const activeNotesList = document.getElementById('activeNotesList');
    const completedNotesList = document.getElementById('completedNotesList');
    const addNoteBtn = document.getElementById('addNewNoteBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const btnNoteText = document.getElementById('btnNoteText');
    const notesCountLabel = document.getElementById('notesCountLabel');
    const notesLoading = document.getElementById('notesLoading');

    let quill = null;
    let editingNoteId = null;

    if (notesModal) {
        // Initialize Quill
        if (!quill) {
            quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Write your reminder here...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['clean']
                    ],
                    keyboard: {
                        bindings: {
                            handleEnter: {
                                key: 13,
                                handler: function () {
                                    saveNote();
                                }
                            }
                        }
                    }
                }
            });
        }

        notesModal.addEventListener('show.bs.modal', loadNotes);
        addNoteBtn.addEventListener('click', saveNote);
        cancelEditBtn.addEventListener('click', resetForm);

        async function saveNote() {
            const content = quill.root.innerHTML.trim();
            if (content === '<p><br></p>' || !content) return;

            addNoteBtn.disabled = true;
            const url = editingNoteId ? `/notes/${editingNoteId}` : '/notes';
            const method = editingNoteId ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content })
                });
                const res = await response.json();
                if (res.success) {
                    resetForm();
                    loadNotes();
                }
            } catch (e) { console.error(e); }
            addNoteBtn.disabled = false;
        }

        function resetForm() {
            quill.setContents([]);
            editingNoteId = null;
            btnNoteText.textContent = 'Add Reminder';
            cancelEditBtn.classList.add('d-none');
            addNoteBtn.querySelector('i').classList.replace('bi-check-lg', 'bi-plus-lg');
        }

        async function loadNotes() {
            notesLoading.classList.remove('d-none');
            activeNotesList.innerHTML = '';
            completedNotesList.innerHTML = '';

            try {
                const response = await fetch('/notes');
                const notes = await response.json();

                if (notesCountLabel) notesCountLabel.textContent = `${notes.length} reminders found`;

                if (notes.length === 0) {
                    activeNotesList.innerHTML = `
                        <div class="text-center py-4 opacity-50">
                            <i class="bi bi-clipboard-x mb-2" style="font-size: 2rem;"></i>
                            <p class="small mb-0">No reminders yet.</p>
                        </div>`;
                    notesLoading.classList.add('d-none');
                    return;
                }

                const active = notes.filter(n => !n.is_checked);
                const completed = notes.filter(n => n.is_checked);

                renderNoteSection(activeNotesList, active);
                renderNoteSection(completedNotesList, completed);

            } catch (e) { console.error(e); }
            notesLoading.classList.add('d-none');
        }

        function renderNoteSection(container, notes) {
            if (notes.length === 0) {
                container.innerHTML = '<p class="text-muted small text-center py-2 opacity-50">Empty</p>';
                return;
            }

            container.innerHTML = notes.map(note => `
                <div class="note-card ${note.is_checked ? 'checked-card' : ''}" data-id="${note.id}">
                    <div class="d-flex align-items-start gap-3 flex-grow-1">
                        <div class="form-check mb-0">
                            <input class="form-check-input note-checkbox" type="checkbox" data-id="${note.id}" ${note.is_checked ? 'checked' : ''}>
                        </div>
                        <div class="note-text ${note.is_checked ? 'checked' : ''}" data-id="${note.id}">
                            ${note.content}
                        </div>
                    </div>
                    <div class="action-btns ms-2">
                        <button class="btn-note-action edit-note" data-id="${note.id}" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn-note-action delete delete-note" data-id="${note.id}" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');

            // Listeners
            container.querySelectorAll('.note-checkbox').forEach(cb => {
                cb.addEventListener('change', async (e) => {
                    const id = e.target.dataset.id;
                    try {
                        await fetch(`/notes/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ is_checked: e.target.checked })
                        });
                        loadNotes();
                    } catch (e) { console.error(e); }
                });
            });

            container.querySelectorAll('.edit-note').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.dataset.id;
                    const note = (await (await fetch('/notes')).json()).find(n => n.id == id);
                    if (note) {
                        editingNoteId = id;
                        quill.root.innerHTML = note.content;
                        btnNoteText.textContent = 'Update';
                        cancelEditBtn.classList.remove('d-none');
                        addNoteBtn.querySelector('i').classList.replace('bi-plus-lg', 'bi-check-lg');
                        notesModal.querySelector('.modal-body').scrollTop = 0;
                    }
                });
            });

            container.querySelectorAll('.delete-note').forEach(btn => {
                btn.addEventListener('click', async () => {
                    if (!confirm('Discard this reminder?')) return;
                    const id = btn.dataset.id;
                    try {
                        await fetch(`/notes/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        loadNotes();
                    } catch (e) { console.error(e); }
                });
            });
        }
    }
});

// ==== EXPENSES BAR DETAILED CHART (Managed by own listener) ====
document.addEventListener("DOMContentLoaded", function () {
    let barChart = null;
    const isDark = () => document.documentElement.getAttribute("data-bs-theme") === "dark";

    window.fetchDataAndRenderChart = function (month, year) {
        const chartElement = document.querySelector("#barJenisPengeluaran");
        if (!chartElement) return;

        fetch(`/dashboard/jenis-pengeluaran?month=${month}&year=${year}`)
            .then(res => res.json())
            .then(data => {
                if (!data || data.length === 0) {
                    if (barChart) barChart.destroy();
                    chartElement.innerHTML = '<div class="text-center p-5 text-muted">No data available</div>';
                    return;
                }

                const labels = data.map(item => item.pengeluaran_nama);
                const values = data.map(item => parseFloat(item.total));

                const options = {
                    chart: { type: 'bar', height: 350, foreColor: isDark() ? '#ddd' : '#333' },
                    series: [{ name: 'Total', data: values }],
                    plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                    xaxis: { categories: labels },
                    colors: ['#667eea'],
                    tooltip: { theme: isDark() ? 'dark' : 'light' }
                };

                if (barChart) barChart.destroy();
                barChart = new ApexCharts(chartElement, options);
                barChart.render();
            });
    };

    const filterBulan = document.getElementById("filterBulan");
    const filterTahun = document.getElementById("filterTahun");
    if (filterBulan && filterTahun) {
        window.fetchDataAndRenderChart(filterBulan.value, filterTahun.value);
    }
});

// ==== GROWTH DETAIL MODAL HANDLER ====
document.addEventListener("DOMContentLoaded", function () {
    const growthTriggers = document.querySelectorAll('.growth-detail-trigger');
    growthTriggers.forEach(trigger => {
        trigger.addEventListener('click', function () {
            const title = this.dataset.title;
            const current = parseFloat(this.dataset.current);
            const last = parseFloat(this.dataset.last);
            const percent = parseFloat(this.dataset.percent);
            const type = this.dataset.type;

            document.getElementById('growthDetailTitle').textContent = title;
            document.getElementById('growthPercentValue').textContent = (percent > 0 ? '+' : '') + percent + '%';
            document.getElementById('currentPeriodValue').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(current);
            document.getElementById('lastPeriodValue').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(last);
            document.getElementById('growthDiffValue').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(current - last);

            const badge = document.getElementById('growthPercentBadge');
            const icon = document.getElementById('growthIcon');
            const alert = document.getElementById('growthSummaryAlert');
            const summaryIcon = document.getElementById('growthSummaryIcon');
            const summaryText = document.getElementById('growthSummaryText');

            // Reset classes
            badge.className = 'd-inline-flex align-items-center gap-1 rounded-pill px-3 py-1 fw-bold fs-5 shadow-sm';
            alert.className = 'alert border-0 py-3 mb-0 rounded-4 d-flex align-items-start gap-3';

            let isPositive = percent > 0;
            // For expenses, positive growth is "bad" (red/danger), negative is "good" (green/success)
            let isGood = type === 'expense' ? !isPositive : isPositive;

            if (percent === 0) {
                badge.classList.add('bg-secondary-light');
                icon.className = 'bi bi-dash';
                alert.classList.add('alert-secondary');
                summaryIcon.className = 'bi bi-info-circle-fill text-secondary';
                summaryText.textContent = 'Tidak ada perubahan signifikan dibandingkan periode yang sama bulan lalu.';
            } else if (isGood) {
                badge.classList.add('bg-success-light');
                icon.className = isPositive ? 'bi bi-arrow-up-circle-fill' : 'bi bi-arrow-down-circle-fill';
                alert.classList.add('alert-success');
                summaryIcon.className = 'bi bi-check-circle-fill text-success';
                summaryText.textContent = type === 'expense'
                    ? 'Bagus! Pengeluaran Anda menurun dibandingkan periode yang sama bulan lalu.'
                    : 'Luar biasa! Pendapatan Anda meningkat dibandingkan periode yang sama bulan lalu.';
            } else {
                badge.classList.add('bg-danger-light');
                icon.className = isPositive ? 'bi bi-arrow-up-circle-fill' : 'bi bi-arrow-down-circle-fill';
                alert.classList.add('alert-danger');
                summaryIcon.className = 'bi bi-exclamation-triangle-fill text-danger';
                summaryText.textContent = type === 'expense'
                    ? 'Waspada! Pengeluaran Anda meningkat dibandingkan periode yang sama bulan lalu.'
                    : 'Perhatian! Pendapatan Anda menurun dibandingkan periode yang sama bulan lalu.';
            }

            const modal = new bootstrap.Modal(document.getElementById('growthDetailModal'));
            modal.show();
        });
    });
});
