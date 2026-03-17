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
                document.getElementById('summary-hari-ini').textContent = res.data.hari_ini;
                toggleBtn.innerHTML = res.show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            } catch (e) { console.error(e); }
            toggleBtn.disabled = false;
        });
    }

    // ==== NOTES MANAGEMENT AJAX ====
    const notesModal = document.getElementById('notesModal');
    const notesList = document.getElementById('notesList');
    const newNoteInput = document.getElementById('newNoteContent');
    const addNoteBtn = document.getElementById('addNewNoteBtn');

    if (notesModal) {
        notesModal.addEventListener('show.bs.modal', loadNotes);

        addNoteBtn.addEventListener('click', async () => {
            const content = newNoteInput.value.trim();
            if (!content) return;

            addNoteBtn.disabled = true;
            try {
                const response = await fetch('/notes', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content })
                });
                const res = await response.json();
                if (res.success) {
                    newNoteInput.value = '';
                    loadNotes();
                }
            } catch (e) { console.error(e); }
            addNoteBtn.disabled = false;
        });

        async function loadNotes() {
            notesList.innerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border text-primary spinner-border-sm mb-3" role="status"></div><p class="small mb-0">Refreshing reminders...</p></div>';
            try {
                const response = await fetch('/notes');
                const notes = await response.json();

                const countLabel = document.getElementById('notesCountLabel');
                if (countLabel) countLabel.textContent = `${notes.length} items total`;

                if (notes.length === 0) {
                    notesList.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-check mb-2 text-muted opacity-25" style="font-size: 3rem;"></i>
                            <p class="text-muted small">All caught up! No active reminders.</p>
                        </div>`;
                    return;
                }

                notesList.innerHTML = notes.map(note => `
                    <div class="note-card ${note.is_checked ? 'checked-card' : ''}" data-id="${note.id}">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <div class="form-check mb-0">
                                <input class="form-check-input note-checkbox" type="checkbox" data-id="${note.id}" ${note.is_checked ? 'checked' : ''} style="cursor: pointer; width: 1.25rem; height: 1.25rem;">
                            </div>
                            <span class="note-text ${note.is_checked ? 'checked' : 'unchecked'}" data-id="${note.id}">
                                ${note.content}
                            </span>
                        </div>
                        <button class="btn btn-link btn-delete-note border-0 shadow-none delete-note" data-id="${note.id}">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                `).join('');

                // Add event listeners for checkboxes
                notesList.querySelectorAll('.note-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', async (e) => {
                        const id = e.target.dataset.id;
                        const isChecked = e.target.checked;
                        const textSpan = notesList.querySelector(`.note-text[data-id="${id}"]`);
                        const noteCard = notesList.querySelector(`.note-card[data-id="${id}"]`);

                        // Optimistic UI update
                        if (isChecked) {
                            textSpan.classList.remove('unchecked');
                            textSpan.classList.add('checked');
                            noteCard.classList.add('checked-card');
                        } else {
                            textSpan.classList.remove('checked');
                            textSpan.classList.add('unchecked');
                            noteCard.classList.remove('checked-card');
                        }

                        try {
                            await fetch(`/notes/${id}`, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ is_checked: isChecked ? 1 : 0 })
                            });
                        } catch (e) { console.error(e); }
                    });
                });

                // Add event listeners for delete buttons
                notesList.querySelectorAll('.delete-note').forEach(btn => {
                    btn.addEventListener('click', async (e) => {
                        const id = btn.dataset.id;
                        if (!confirm('Are you sure you want to delete this reminder?')) return;

                        try {
                            const response = await fetch(`/notes/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });
                            const res = await response.json();
                            if (res.success) loadNotes();
                        } catch (e) { console.error(e); }
                    });
                });

            } catch (e) { console.error(e); }
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
