@push('anggaran-css')
<link rel="stylesheet" href="{{ asset('css/dashboard/anggaran.css') }}?v={{ filemtime(public_path('css/dashboard/anggaran.css')) }}">
@endpush

<div class="mb-3 d-flex gap-3 align-items-center flex-wrap">
    <label class="fw-semibold">Filter Periode Anggaran:</label>

    <select id="filterTanggal" class="form-control" style="max-width: 300px;">
        <option value="">Semua Data</option>
        @foreach($filterOptions as $row)
        <option value="{{ $row->tanggal_mulai }}_{{ $row->tanggal_selesai }}">
            {{ $row->tanggal_mulai }} s/d {{ $row->tanggal_selesai }}
        </option>
        @endforeach
    </select>
</div>

<div id="chartAnggaran"></div>


@push('anggaran.scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {

        function loadChart(filter = "") {
            fetch("{{ route('anggaran.chart') }}?filter=" + filter)
                .then(res => res.json())
                .then(data => {


                    if (!data.labels.length) {
                        document.querySelector("#chartAnggaran").innerHTML =
                            "<p class='text-muted text-center'>Tidak ada data anggaran.</p>";
                        return;
                    }




                    const anggaran = data.datasets[0].data;
                    const realisasiRaw = data.datasets[1].data;

                    // Realisasi tidak boleh lebih dari anggaran (untuk stacked)
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
                            height: 420,
                            toolbar: {
                                show: true
                            },
                            foreColor: isDark ? '#e0e0e0' : '#333'
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                barHeight: "45%"
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
                                style: { colors: isDark ? '#e0e0e0' : '#333' },
                                formatter: value =>
                                    "Rp " + new Intl.NumberFormat("id-ID").format(value)
                            }
                        },
                        yaxis: {
                            labels: {
                                style: { colors: isDark ? '#e0e0e0' : '#333' }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            style: {
                                fontSize: '12px',
                                fontWeight: 'bold',
                                colors: ['#fff']
                            },
                            formatter: value => {
                                if (value === 0) return "";
                                return "Rp " + new Intl.NumberFormat("id-ID").format(value);
                            }
                        },
                        legend: {
                            position: "top",
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
                            borderColor: isDark ? '#444' : '#e0e0e0'
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