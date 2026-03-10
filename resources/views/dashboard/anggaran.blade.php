@push('anggaran-css')
<link rel="stylesheet" href="{{ asset('css/dashboard/anggaran.css') }}?v={{ filemtime(public_path('css/dashboard/anggaran.css')) }}">
@endpush

<div id="chartAnggaran" style="min-height: 350px;"></div>


@push('anggaran.scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {

        function loadChart(filter = "") {
            fetch("{{ route('anggaran.chart') }}?filter=" + filter)
                .then(res => res.json())
                .then(data => {
                    const isMobile = window.innerWidth < 768;

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

                    const options = {
                        chart: {
                            type: 'bar',
                            stacked: true,
                            height: isMobile ? 350 : 420,
                            toolbar: {
                                show: !isMobile
                            },
                            foreColor: isDark ? '#e0e0e0' : '#333'
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                barHeight: isMobile ? "65%" : "45%",
                                borderRadius: 4
                            }
                        },
                        stroke: {
                            width: 1,
                            colors: [isDark ? '#333' : '#fff']
                        },
                        series: [{
                                name: "Realisasi",
                                data: realisasi,
                                color: "#3B82F6"
                            },
                            {
                                name: "Sisa",
                                data: sisa,
                                color: "#22C55E"
                            },
                            {
                                name: "Over Budget",
                                data: overBudget,
                                color: "#DC2626"
                            }
                        ],
                        xaxis: {
                            categories: data.labels,
                            labels: {
                                show: !isMobile || data.labels.length < 5,
                                style: { 
                                    colors: isDark ? '#e0e0e0' : '#333',
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
                                    colors: isDark ? '#e0e0e0' : '#333',
                                    fontSize: isMobile ? '10px' : '12px'
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
                                radius: 8
                            }
                        },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: {
                                formatter: value =>
                                    "Rp " + new Intl.NumberFormat("id-ID").format(value)
                            }
                        },
                        grid: {
                            borderColor: isDark ? '#444' : '#e0e0e0',
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

        loadChart();
    });
</script>
@endpush