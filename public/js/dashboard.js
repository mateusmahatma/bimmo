// Cash Flow
document.addEventListener("DOMContentLoaded", function () {
    // Ambil data dari endpoint /dashboard/line-data
    fetch("/dashboard/line-data")
        .then((response) => response.json())
        .then((data) => {
            // Hitung selisih pemasukan dan pengeluaran untuk setiap label
            var selisih = data.pemasukan.map(
                (value, index) => value - data.pengeluaran[index]
            );

            // Siapkan opsi untuk chart
            var options = {
                chart: {
                    type: "bar", // Tipe grafik yang digunakan
                    height: 350, // Tinggi chart
                    toolbar: {
                        show: true, // Menampilkan toolbar
                        tools: {
                            download: true, // Opsi untuk download
                        },
                    },
                },
                plotOptions: {
                    bar: {
                        columnWidth: "45%", // Lebar kolom
                        borderRadius: 10, // Sudut rounded pada seluruh bar
                    },
                },
                series: [
                    {
                        name: "Pemasukan",
                        data: data.pemasukan,
                    },
                    {
                        name: "Pengeluaran",
                        data: data.pengeluaran,
                    },
                    {
                        name: "Selisih (Pemasukan - Pengeluaran)",
                        data: selisih,
                    },
                ],
                xaxis: {
                    categories: data.labels, // Menampilkan bulan
                    // title: {
                    //     text: "Bulan", // Label X-axis
                    // },
                    // labels: {
                    //     style: {
                    //         colors: "#333", // Warna label sumbu X
                    //         fontSize: "12px", // Ukuran font label
                    //         fontWeight: "bold", // Berat font label
                    //     },
                    // },
                },
                yaxis: {
                    title: {
                        text: "Nominal", // Label Y-axis
                    },
                    labels: {
                        style: {
                            colors: "#333", // Warna label sumbu Y
                            fontSize: "12px", // Ukuran font label
                        },
                    },
                },
                tooltip: {
                    theme: "light", // Tema tooltip gelap
                    y: {
                        formatter: function (val) {
                            return "Rp " + val.toLocaleString(); // Format mata uang
                        },
                    },
                },
                fill: {
                    opacity: 0.9, // Opacity yang sedikit lebih tebal
                },
                dataLabels: {
                    enabled: false, // Menampilkan data labels di atas kolom
                    style: {
                        colors: ["#fff"], // Warna data labels
                        fontSize: "10px",
                    },
                },
                legend: {
                    position: "top", // Posisi legend di atas
                    horizontalAlign: "center", // Menyusun legend secara horizontal
                    fontSize: "14px",
                },
            };

            // Inisialisasi chart
            var chart = new ApexCharts(
                document.querySelector("#columnChart"),
                options
            );
            chart.render();
        })
        .catch((error) => {
            console.error("Error fetching chart data:", error);
        });
});

// Pie Chart
// document.addEventListener("DOMContentLoaded", function () {
//     fetch("/dashboard/pie-data")
//         .then((response) => response.json())
//         .then((data) => {
//             if (data.labels.length === 0 || data.data.length === 0) {
//                 document.getElementById("noDataMessagePie").style.display =
//                     "block";
//                 return;
//             }

//             var ctx = document.getElementById("pieChart").getContext("2d");
//             var backgroundColors = generateRandomColors(data.labels.length);

//             var polarAreaChart = new Chart(ctx, {
//                 type: "pie",
//                 data: {
//                     labels: data.labels,
//                     datasets: [
//                         {
//                             data: data.data,
//                             backgroundColor: backgroundColors,
//                             borderWidth: 0,
//                         },
//                     ],
//                 },
//                 options: {
//                     plugins: {
//                         legend: {
//                             display: false,
//                         },
//                         tooltip: {
//                             enabled: true,
//                             callbacks: {
//                                 label: function (tooltipItem) {
//                                     var label =
//                                         data.labels[tooltipItem.dataIndex] ||
//                                         "";
//                                     var value =
//                                         data.data[tooltipItem.dataIndex] || "";
//                                     return `${label}: ${value}`;
//                                 },
//                             },
//                         },
//                         datalabels: {
//                             color: "#fff", // Text color
//                             anchor: "end",
//                             align: "start",
//                             offset: -10,
//                             borderWidth: 2,
//                             borderColor: "#fff",
//                             borderRadius: 25,
//                             backgroundColor: (context) =>
//                                 context.dataset.backgroundColor,
//                             font: {
//                                 weight: "bold",
//                                 size: "12",
//                             },
//                             formatter: function (value, context) {
//                                 var label =
//                                     context.chart.data.labels[
//                                         context.dataIndex
//                                     ];
//                                 return label + "\n" + value + "%";
//                             },
//                         },
//                     },
//                     hover: {
//                         mode: "index", // Ensures the tooltip shows on hover
//                         intersect: false,
//                         onHover: function (event, activeElements) {
//                             if (activeElements.length > 0) {
//                                 const element = activeElements[0];
//                                 const dataset =
//                                     polarAreaChart.data.datasets[
//                                         element.datasetIndex
//                                     ];
//                                 dataset.borderWidth = 2; // Set border width on hover
//                                 polarAreaChart.update();
//                             } else {
//                                 polarAreaChart.data.datasets.forEach(
//                                     (dataset) => (dataset.borderWidth = 0)
//                                 ); // Reset border width
//                                 polarAreaChart.update();
//                             }
//                         },
//                     },
//                     animation: {
//                         animateScale: true,
//                         animateRotate: true,
//                     },
//                     responsive: true,
//                     maintainAspectRatio: false,
//                 },
//             });
//         })
//         .catch((error) => {
//             console.error("Error fetching polar area chart data:", error);
//         });
// });

// Expense Chart
document.addEventListener("DOMContentLoaded", function () {
    fetch("/dashboard/chart-data")
        .then((response) => response.json())
        .then((data) => {
            var options = {
                chart: {
                    type: "line",
                    height: 350,
                },
                series: [
                    {
                        name: "Pengeluaran",
                        data: data.data_pengeluaran, // Menggunakan data pengeluaran
                    },
                    {
                        name: "Pemasukan",
                        data: data.data_pemasukan, // Menggunakan data pemasukan
                    },
                ],
                xaxis: {
                    categories: data.labels, // Menampilkan label bulan dan tahun
                },
                yaxis: [
                    {
                        title: {
                            text: "Pengeluaran (IDR)",
                        },
                    },
                    {
                        opposite: true,
                        title: {
                            text: "Pemasukan (IDR)",
                        },
                    },
                ],
            };

            var chart = new ApexCharts(
                document.querySelector("#barChart"),
                options
            );
            chart.render();
        })
        .catch((error) => {
            console.error("Error fetching chart data:", error);
        });
});

// Transaksi Hari ini
$(document).ready(function () {
    function getTodayTransactions() {
        $.ajax({
            url: "/dashboard/todayTransactions",
            type: "GET",
            dataType: "json",
            success: function (data) {
                populateTodayTransactionsTable(data);
            },
            error: function (error) {
                console.error("Error fetching today transactions:", error);
            },
        });
    }

    function formatNumberWithSeparator(number) {
        if (!number) return "0";
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function formatKeterangan(keterangan) {
        if (!keterangan) return "-";
        if (typeof keterangan !== "string") return keterangan;

        var lines = keterangan.split("\n");
        var renderedText = "";
        lines.forEach(function (line, index) {
            renderedText += index + 1 + ". " + line + "<br>";
        });
        return renderedText;
    }

    function populateTodayTransactionsTable(data) {
        var tableBody = $("#todayTransactionsTable tbody");
        tableBody.empty();

        var cardTitle = $(".card-transaksi h3");

        if (data.length === 0) {
            var noDataRow =
                "<tr>" +
                '<td class="text-center" colspan="6">No data available in table</td>' +
                "</tr>";
            tableBody.append(noDataRow);
        } else {
            $.each(data, function (key, transaction) {
                var row =
                    "<tr>" +
                    '<td class="text-center">' +
                    (key + 1) +
                    "</td>" +
                    '<td class="text-center">' +
                    (transaction.pemasukan ? transaction.pemasukan : "-") +
                    "</td>" +
                    '<td class="text-center">' +
                    (transaction.nominal_pemasukan
                        ? formatNumberWithSeparator(
                              transaction.nominal_pemasukan
                          )
                        : "0") +
                    "</td>" +
                    '<td class="text-center">' +
                    (transaction.pengeluaran ? transaction.pengeluaran : "-") +
                    "</td>" +
                    '<td class="text-center">' +
                    (transaction.nominal
                        ? formatNumberWithSeparator(transaction.nominal)
                        : "0") +
                    "</td>" +
                    '<td class="text-left">' +
                    (transaction.keterangan
                        ? formatKeterangan(transaction.keterangan)
                        : "-") +
                    "</td>" +
                    "</tr>";
                tableBody.append(row);
            });
            cardTitle.text("Transaksi Hari ini");
        }
    }
    getTodayTransactions();
});

// Dark Mode
document.addEventListener("DOMContentLoaded", function () {
    const darkModeDropdown = document.getElementById("darkModeDropdown");

    const storedMode = localStorage.getItem("darkMode");
    const isDarkMode = storedMode === "enabled";

    if (isDarkMode) {
        enableDarkMode();
        darkModeDropdown.style.color = "white";
        darkModeDropdown.value = "dark";
    }

    darkModeDropdown.addEventListener("change", function () {
        const selectedMode = darkModeDropdown.value;
        if (selectedMode === "dark") {
            enableDarkMode();
            darkModeDropdown.style.color = "white";
            localStorage.setItem("darkMode", "enabled");
        } else {
            disableDarkMode();
            darkModeDropdown.style.color = "";
            localStorage.setItem("darkMode", null);
        }
    });

    function enableDarkMode() {
        document.body.classList.add("dark-mode");
    }

    function disableDarkMode() {
        document.body.classList.remove("dark-mode");
    }
});

document.addEventListener("DOMContentLoaded", () => {
    initializeNominalDisplay();
});

// Show Hide Nominal
function toggleNominal() {
    const h3Elements = document.querySelectorAll(".box-info h3");
    let isHidden = false;

    h3Elements.forEach((h3) => {
        if (h3.textContent.includes("*")) {
            h3.textContent = h3.getAttribute("data-value");
            isHidden = false;
        } else {
            h3.textContent = "****";
            isHidden = true;
        }
    });

    localStorage.setItem("nominalHidden", isHidden ? "true" : "false");
}

function initializeNominalDisplay() {
    const isHidden = localStorage.getItem("nominalHidden") === "true";
    const h3Elements = document.querySelectorAll(".box-info h3");

    h3Elements.forEach((h3) => {
        if (isHidden) {
            h3.textContent = "****";
        } else {
            h3.textContent = h3.getAttribute("data-value");
        }
    });
}

// Kompas
var options = {
    series: [
        {
            name: "Actual",
            data: [
                {
                    x: "Net Worth",
                    y: 12,
                    goals: [
                        {
                            name: "Target",
                            value: 30,
                            strokeWidth: 2,
                            strokeDashArray: 2,
                            strokeColor: "#775DD0",
                        },
                    ],
                },
                {
                    x: "Saving Rate",
                    y: 44,
                    goals: [
                        {
                            name: "Target",
                            value: 30,
                            strokeWidth: 5,
                            strokeHeight: 10,
                            strokeColor: "#775DD0",
                        },
                    ],
                },
                {
                    x: "FIRE Number",
                    y: 54,
                    goals: [
                        {
                            name: "Target",
                            value: 50,
                            strokeWidth: 10,
                            strokeHeight: 0,
                            strokeLineCap: "round",
                            strokeColor: "#775DD0",
                        },
                    ],
                },
                {
                    x: "Debt-to-Income Ratio",
                    y: 66,
                    goals: [
                        {
                            name: "Target",
                            value: 35,
                            strokeWidth: 10,
                            strokeHeight: 0,
                            strokeLineCap: "round",
                            strokeColor: "#775DD0",
                        },
                    ],
                },
                {
                    x: "Emergency Fund",
                    y: 81,
                    goals: [
                        {
                            name: "Target",
                            value: 66,
                            strokeWidth: 10,
                            strokeHeight: 0,
                            strokeLineCap: "round",
                            strokeColor: "#775DD0",
                        },
                    ],
                },
                {
                    x: "Investment Growth",
                    y: 67,
                    goals: [
                        {
                            name: "Target",
                            value: 8,
                            strokeWidth: 5,
                            strokeHeight: 10,
                            strokeColor: "#775DD0",
                        },
                    ],
                },
            ],
        },
    ],
    chart: {
        height: 350,
        type: "bar",
    },
    plotOptions: {
        bar: {
            horizontal: true,
            borderRadius: 10, // Menambahkan border radius untuk sudut tumpul
        },
    },
    colors: ["#00E396"],
    dataLabels: {
        formatter: function (val, opt) {
            const goals =
                opt.w.config.series[opt.seriesIndex].data[opt.dataPointIndex]
                    .goals;

            if (goals && goals.length) {
                return `${val} / ${goals[0].value}`;
            }
            return val;
        },
    },
    legend: {
        show: true,
        showForSingleSeries: true,
        customLegendItems: ["Actual", "Target"],
        markers: {
            fillColors: ["#00E396", "#775DD0"],
        },
    },
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
