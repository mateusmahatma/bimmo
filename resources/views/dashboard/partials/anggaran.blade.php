@php
    $uiStyle = auth()->user()->ui_style ?? 'corporate';
@endphp

@push('anggaran-css')
<link rel="stylesheet" href="{{ asset('css/dashboard/anggaran.css') }}?v={{ filemtime(public_path('css/dashboard/anggaran.css')) }}">
@endpush

<div id="chartAnggaran" style="min-height: 350px;"></div>

{{-- BURN RATE SUMMARY --}}
<div id="burnRateSummary" class="mt-4 border-top pt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-speedometer2 text-primary"></i> {{ __('Burn Rate Summary') }}
        </h6>
        <span class="opacity-75 small" style="font-size: 0.7rem;">{{ __('Berdasarkan Periode Terpilih') }}</span>
    </div>
    <div class="row row-cols-1 row-cols-md-2 g-3" id="burnRateList"></div>
</div>

@push('anggaran.scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const uiStyle = "{{ $uiStyle }}";

        function formatIDR(val) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0
            }).format(val);
        }

        function renderBurnRate(items) {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const container = document.getElementById('burnRateList');
            if (!container) return;
            container.innerHTML = "";

            const filteredItems = items.filter(it => it.burn_rate !== null)
                .sort((a, b) => {
                    // Urutkan berdasarkan persentase pengeluaran tertinggi
                    if (b.burn_rate.spent_percentage !== a.burn_rate.spent_percentage) {
                        return b.burn_rate.spent_percentage - a.burn_rate.spent_percentage;
                    }
                    // Jika persentase sama, urutkan berdasarkan nominal pengeluaran tertinggi
                    return b.burn_rate.total_spent - a.burn_rate.total_spent;
                });

            if (filteredItems.length === 0) {
                container.innerHTML = `<div class="col-12"><p class="text-muted small text-center">Pilih periode awal/sedang berjalan untuk melihat burn rate.</p></div>`;
                return;
            }

            filteredItems.forEach(item => {
                const br = item.burn_rate;
                const statusColor = br.is_over_burning ? 'danger' : (br.is_behind_pace ? 'warning' : 'success');
                const statusText = br.is_over_burning ? 'Over Budget' : (br.is_behind_pace ? 'Waspada' : 'Aman');
                
                // Custom Premium Aesthetic
                const cardBiggerBg = isDark ? '#14171a' : '#ffffff';
                const cardInnerBg = isDark ? '#1a1d21' : '#ffffff';
                const subBoxBg = isDark ? 'rgba(255, 255, 255, 0.03)' : 'rgba(0, 0, 0, 0.02)';
                const borderColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)';
                const textColor = isDark ? '#e2e8f0' : '#1e293b';
                const mutedColor = isDark ? '#94a3b8' : '#64748b';

                let breakdownHtml = "";
                if (item.kategori_breakdown && item.kategori_breakdown.length > 0) {
                    const cat = item.kategori_breakdown[0];
                    breakdownHtml = `
                        <div class="mt-3 pt-3" style="border-top: 1px solid ${borderColor};">
                            <p class="text-danger mb-2 fw-bold d-flex align-items-center gap-1" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="bi bi-exclamation-triangle-fill"></i> Pengeluaran Terboros
                            </p>
                            <div class="p-2 rounded" style="background: rgba(var(--bs-danger-rgb), 0.08); border: 1px solid rgba(var(--bs-danger-rgb), 0.15);">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                        <i class="bi bi-tag-fill text-danger" style="font-size: 0.8rem;"></i>
                                        <span class="fw-bold text-truncate small" style="font-size: 0.75rem; color: ${textColor};">${cat.nama}</span>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="fw-bold text-danger" style="font-size: 0.8rem;">${formatIDR(cat.nominal)}</div>
                                        <div class="fw-bold opacity-75" style="font-size: 0.65rem; color: ${statusColor === 'danger' ? 'var(--bs-danger)' : mutedColor};">${cat.persentase.toFixed(1)}% dari budget</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                const card = `
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm" style="background: ${cardInnerBg}; border: 1px solid ${borderColor} !important; border-radius: 16px; transition: all 0.3s ease; overflow: hidden;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="card-title mb-0 fw-bold text-truncate" style="font-size: 0.95rem; max-width: 150px; color: ${textColor};" title="${item.nama_anggaran}">${item.nama_anggaran}</h6>
                                    <span class="badge bg-${statusColor}-subtle text-${statusColor} border border-${statusColor}-subtle px-2 py-1" style="font-size: 0.7rem; border-radius: 20px;">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> ${statusText}
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span style="color: ${mutedColor}; font-size: 0.75rem;">Progres Budget</span>
                                        <span class="fw-bold text-${statusColor}">${br.spent_percentage.toFixed(1)}%</span>
                                    </div>
                                    <div class="progress" style="height: 10px; border-radius: 10px; background-color: ${subBoxBg}; overflow: hidden;">
                                        <div class="progress-bar bg-${statusColor} progress-bar-striped progress-bar-animated" role="progressbar" style="width: ${Math.min(br.spent_percentage, 100)}%; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="p-2 rounded-3" style="background: ${subBoxBg}; border: 1px solid ${borderColor};">
                                            <div style="color: ${mutedColor}; font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">Terpakai</div>
                                            <div class="fw-bold" style="font-size: 0.85rem; color: ${textColor};">${formatIDR(br.total_spent)}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 rounded-3" style="background: ${subBoxBg}; border: 1px solid ${borderColor};">
                                            <div style="color: ${mutedColor}; font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">Budget</div>
                                            <div class="fw-bold" style="font-size: 0.85rem; color: ${textColor};">${formatIDR(br.total_budget)}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary);">
                                            <i class="bi bi-calendar-event" style="font-size: 0.9rem;"></i>
                                        </div>
                                        <div>
                                            <div style="color: ${mutedColor}; font-size: 0.65rem; font-weight: 600;">Sisa Waktu</div>
                                            <div class="fw-bold" style="font-size: 0.85rem; color: ${textColor};">${br.days_remaining} Hari</div>
                                        </div>
                                    </div>
                                </div>

                                ${breakdownHtml}

                                <div class="mt-3 pt-3 text-end" style="border-top: 1px solid ${borderColor};">
                                    <a href="/kalkulator/${item.hash}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm" style="font-size: 0.75rem; font-weight: 600;">
                                        Detail <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', card);
            });
        }

        function loadChart(filter = "") {
            fetch("{{ route('anggaran.chart') }}?filter=" + filter)
                .then(res => res.json())
                .then(data => {
                    const isMobile = window.innerWidth < 768;

                    // Render Burn Rate Summary Table/List
                    renderBurnRate(data.table || []);

                    if (!data.labels.length) {
                        document.querySelector("#chartAnggaran").innerHTML =
                            "<div class='d-flex align-items-center justify-content-center' style='height: 200px;'><p class='text-muted'>Tidak ada data anggaran.</p></div>";
                        return;
                    }

                    const anggaran = data.datasets[0].data;
                    const realisasiRaw = data.datasets[1].data;

                    const realisasi = realisasiRaw.map((v, i) =>
                        Math.min(v, anggaran[i])
                    );

                    const sisa = anggaran.map((v, i) =>
                        Math.max(v - realisasi[i], 0)
                    );

                    const overBudget = realisasiRaw.map((v, i) =>
                        v > anggaran[i] ? v - anggaran[i] : 0
                    );

                    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                    
                    // Millennial Colors
                    const colors = uiStyle === 'milenial' 
                        ? ["#6366F1", "#10B981", "#F43F5E"] // Indigo, Emerald, Rose
                        : ["#3B82F6", "#22C55E", "#DC2626"]; // Original colors

                    const options = {
                        chart: {
                            type: 'bar',
                            stacked: true,
                            height: isMobile ? 350 : 420,
                            toolbar: {
                                show: !isMobile
                            },
                            foreColor: isDark ? '#e0e0e0' : (uiStyle === 'milenial' ? '#666' : '#333'),
                            fontFamily: uiStyle === 'milenial' ? "'Inter', 'Outfit', sans-serif" : 'inherit'
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                barHeight: isMobile ? "65%" : "45%",
                                borderRadius: uiStyle === 'milenial' ? 8 : 4
                            }
                        },
                        stroke: {
                            width: uiStyle === 'milenial' ? 0 : 1,
                            colors: [isDark ? '#333' : '#fff']
                        },
                        series: [{
                                name: "Realisasi",
                                data: realisasi,
                                color: colors[0]
                            },
                            {
                                name: "Sisa",
                                data: sisa,
                                color: colors[1]
                            },
                            {
                                name: "Over Budget",
                                data: overBudget,
                                color: colors[2]
                            }
                        ],
                        xaxis: {
                            categories: data.labels,
                            labels: {
                                show: !isMobile || data.labels.length < 5,
                                style: { 
                                    colors: isDark ? '#e0e0e0' : (uiStyle === 'milenial' ? '#999' : '#333'),
                                    fontSize: '10px'
                                },
                                formatter: value => {
                                    if (isMobile && value >= 1000000) return "Rp " + (value / 1000000).toFixed(1) + "jt";
                                    return "Rp " + new Intl.NumberFormat("id-ID").format(value)
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: { 
                                    colors: isDark ? '#e0e0e0' : (uiStyle === 'milenial' ? '#666' : '#333'),
                                    fontSize: isMobile ? '10px' : '12px',
                                    fontWeight: uiStyle === 'milenial' ? 600 : 400
                                },
                                maxWidth: isMobile ? 100 : 200
                            }
                        },
                        dataLabels: {
                            enabled: !isMobile,
                            style: {
                                fontSize: '10px',
                                fontWeight: 'bold'
                            },
                            formatter: value => {
                                if (value === 0) return "";
                                return "Rp " + new Intl.NumberFormat("id-ID").format(value);
                            }
                        },
                        legend: {
                            position: isMobile ? "bottom" : "top",
                            markers: {
                                radius: uiStyle === 'milenial' ? 12 : 8
                            },
                            fontWeight: uiStyle === 'milenial' ? 600 : 400
                        },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: {
                                formatter: value =>
                                    "Rp " + new Intl.NumberFormat("id-ID").format(value)
                            }
                        },
                        grid: {
                            borderColor: isDark ? '#444' : '#efefef',
                            padding: {
                                left: isMobile ? 0 : 20
                            }
                        }
                    };

                    document.querySelector("#chartAnggaran").innerHTML = "";
                    new ApexCharts(document.querySelector("#chartAnggaran"), options).render();
                });
        }

        document.getElementById("filterTanggal")?.addEventListener("change", function() {
            loadChart(this.value);
        });

        document.getElementById("btnSyncAnggaran")?.addEventListener("click", function() {
            const btn = this;
            const icon = btn.querySelector('i');
            
            btn.disabled = true;
            icon.classList.add('fa-spin'); // if using font-awesome
            icon.style.animation = "spin 1s linear infinite"; // fallback for generic animation

            if (!document.getElementById('sync-style')) {
                const style = document.createElement('style');
                style.id = 'sync-style';
                style.innerHTML = `
                    @keyframes spin {
                        from { transform: rotate(0deg); }
                        to { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }

            fetch("{{ route('dashboard.sync-anggaran') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                const filter = document.getElementById("filterTanggal")?.value || "";
                loadChart(filter);
                
                // Show toast if available (assuming a showToast function exists or just use alert)
                if (window.showToast) {
                    window.showToast('Success', data.message, 'success');
                } else {
                    // fall back to default Swal or similar if present
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            })
            .catch(err => {
                console.error("Sync error:", err);
            })
            .finally(() => {
                btn.disabled = false;
                icon.style.animation = "none";
            });
        });

        loadChart();
    });
</script>
@endpush
