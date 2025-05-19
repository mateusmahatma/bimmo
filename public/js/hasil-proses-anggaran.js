$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });


    // $('select[name="filter_pemasukan"]').on("change", function () {
    //     hasilAnggaranTable.ajax.reload();
    // });

    // $('select[name="filter_pengeluaran"]').on("change", function () {
    //     hasilAnggaranTable.ajax.reload();
    // });
});