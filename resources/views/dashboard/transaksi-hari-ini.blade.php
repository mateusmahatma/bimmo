@push('transaksi-css')
<link rel="stylesheet" href="{{ asset('css/dashboard/transaksi.css') }}?v={{ filemtime(public_path('css/dashboard/transaksi.css')) }}">
@endpush

<div class="table-responsive-wrapper">
    <table id="todayTransactionsTable" class="dashboardTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Pendapatan</th>
                <th>Nominal Pendapatan</th>
                <th>Jenis Pengeluaran</th>
                <th>Nominal Pengeluaran</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        function getTodayTransactions() {
            $.ajax({
                url: "/dashboard/todayTransactions",
                type: "GET",
                dataType: "json",
                success: populateTodayTransactionsTable,
                error: function(error) {
                    console.error("Error fetching today transactions:", error);
                }
            });
        }

        function formatNumberWithSeparator(number) {
            return number ? number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") : "0";
        }

        function formatKeterangan(keterangan) {
            if (!keterangan) return "-";
            if (typeof keterangan !== "string") return keterangan;

            return keterangan
                .split("\n")
                .map((line, i) => `${i + 1}. ${line}`)
                .join("<br>");
        }

        function populateTodayTransactionsTable(data) {
            const tbody = $("#todayTransactionsTable tbody");
            tbody.empty();

            if (!data || data.length === 0) {
                tbody.append(`
                <tr>
                    <td class="text-center" colspan="6">No data available in table</td>
                </tr>
            `);
                return;
            }

            data.forEach((transaction, index) => {
                tbody.append(`
                <tr>
                    <td class="text-center">${index + 1}</td>
                    
                    <td class="text-center">
                        ${transaction.pemasukan_relation?.nama ?? "-"}
                    </td>

                    <td class="text-center">
                        ${formatNumberWithSeparator(transaction.nominal_pemasukan)}
                    </td>

                    <td class="text-center">
                        ${transaction.pengeluaran_relation?.nama ?? "-"}
                    </td>

                    <td class="text-center">
                        ${formatNumberWithSeparator(transaction.nominal)}
                    </td>

                    <td class="text-left">
                        ${formatKeterangan(transaction.keterangan)}
                    </td>
                </tr>
            `);
            });
        }

        getTodayTransactions();
    });
</script>