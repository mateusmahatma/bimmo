$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const today = moment();
    const start_date = today.clone().startOf("day");
    const end_date = today.clone().endOf("day");

    // Tampilkan tanggal default di <span>
    $("#daterange span").html(
        start_date.format("YYYY-MM-DD") + " - " + end_date.format("YYYY-MM-DD")
    );

    $("#daterange").daterangepicker(
        {
            startDate: start_date,
            endDate: end_date,
            ranges: {
                "Today": [moment(), moment()],
                "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month")
                ],
                "This Year": [moment().startOf("year"), moment().endOf("year")],
                "Last Year": [
                    moment().subtract(1, "year").startOf("year"),
                    moment().subtract(1, "year").endOf("year")
                ]
            },
            locale: {
                format: "YYYY-MM-DD"
            }
        },
        function (start, end) {
            // Update tampilan tanggal setelah dipilih
            $("#daterange span").html(
                start.format("YYYY-MM-DD") + " - " + end.format("YYYY-MM-DD")
            );

            // Update hidden input untuk disimpan di backend
            $("#tanggal_mulai").val(start.format("YYYY-MM-DD"));
            $("#tanggal_selesai").val(end.format("YYYY-MM-DD"));
        }
    );

    var hasilAnggaranTable = $("#hasilAnggaranTable").DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        serverSide: true,
        processing: true,
        language: {
            processing:
                '<div class="loader-container"><div class="loader"></div></div>',
        },
        ajax: {
            url: "/kalkulator",
            type: "GET",
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
                className: "text-center",
            },
            {
                data: "tanggal_mulai",
                name: "tanggal_mulai",
                className: "text-center",
                render: function (data) {
                    return moment(data).format("dddd, D MMMM YYYY");
                },
            },
            {
                data: "tanggal_selesai",
                name: "tanggal_selesai",
                className: "text-center",
                render: function (data) {
                    return moment(data).format("dddd, D MMMM YYYY");
                },
            },
            {
                data: "nama_anggaran",
                name: "nama_anggaran",
                className: "text-center",
                render: function (data) {
                    return data ? data : "-";
                },
            },
            {
                data: 'nama_jenis_pengeluaran', // ini yang tampil
                name: 'jenis_pengeluaran',      // ini nama kolom asli untuk sort/filter
                className: 'text-center',
                defaultContent: '-'
            },
            {
                data: "persentase_anggaran",
                name: "persentase_anggaran",
                className: "text-center",
                render: function (data) {
                    return data ? data + "%" : "0%";
                },
            },
            {
                data: "nominal_anggaran",
                name: "nominal_anggaran",
                className: "text-center",
                render: function (data) {
                    var numericValue = parseFloat(data);
                    return !isNaN(numericValue)
                        ? numericValue.toLocaleString("id-ID")
                        : "0";
                },
            },
            {
                data: "anggaran_yang_digunakan",
                name: "anggaran_yang_digunakan",
                className: "text-center",
                render: function (data) {
                    var numericValue = parseFloat(data);
                    return !isNaN(numericValue)
                        ? numericValue.toLocaleString("id-ID")
                        : "0";
                },
            },
            {
                data: "sisa_anggaran",
                name: "sisa_anggaran",
                className: "text-center",
            },
            {
                data: "aksi",
                orderable: false,
                searchable: false,
                className: "text-center",
            },
        ],
    });
});

function downloadPDF() {
    var button = document.getElementById("downloadButton");
    var originalText = button.innerHTML;

    button.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
    button.disabled = true;

    $.ajax({
        url: "/kalkulator/cetak_pdf",
        type: "GET",
        xhrFields: {
            responseType: "blob",
        },
        success: function (data) {
            // Membuat link untuk download
            var blob = new Blob([data], { type: "application/pdf" });
            var link = document.createElement("a");
            link.href = window.URL.createObjectURL(blob);
            link.download = "Anggaran_Report_" + new Date().getTime() + ".pdf";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            button.innerHTML = originalText;
            button.disabled = false;
        },
        error: function (error) {
            alert("Terjadi kesalahan: " + error.responseJSON.error);
            button.innerHTML = originalText;
            button.disabled = false;
        },
    });
}

// Proses Hasil Anggaran
document.getElementById('btnProses').addEventListener('click', function (e) {
    const dateRangeText = document.getElementById('daterange').querySelector('span').innerText;

    if (dateRangeText.includes(' - ')) {
        const [start, end] = dateRangeText.split(' - ');
        document.getElementById('tanggal_mulai').value = start.trim();
        document.getElementById('tanggal_selesai').value = end.trim();
    } else {
        alert('Silakan pilih rentang tanggal terlebih dahulu');
        e.preventDefault();  // batalkan submit kalau tanggal belum dipilih
    }
});

