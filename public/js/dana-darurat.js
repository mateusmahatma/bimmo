$(document).ready(function () {
    $('#danaDaruratTable').DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        language: {
            processing: '<div class="loader-container"><div class="loader"></div></div>'
        },
        ajax: {
            url: '/dana-darurat',
            type: 'GET',
            dataSrc: function (json) {
                const total = parseFloat(json.totalDanaDarurat);
                if (!isNaN(total)) {
                    $("#totalDanaDarurat").text(
                        total.toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            minimumFractionDigits: 2
                        })
                    );
                } else {
                    $("#totalDanaDarurat").text("Rp 0,00");
                }

                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'tgl_transaksi_dana_darurat', className: 'text-center', render: data => moment(data).format('YYYY-MM-DD') },
            {
                data: 'jenis_transaksi_dana_darurat', className: 'text-center', name: 'jenis_transaksi_dana_darurat', render: function (data, type, row) {
                    if (data === 'Masuk') {
                        return '<span style="color:green; font-weight:bold;">' + data + '</span>';
                    } else {
                        return '<span style="color:red; font-weight:bold;">' + data + '</span>';
                    }
                }
            },
            { data: 'nominal_dana_darurat', className: 'text-center', render: data => parseFloat(data).toLocaleString("id-ID") },
            { data: 'keterangan', className: 'text-center' },
            { data: 'created_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'updated_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'aksi', className: 'text-center' }
        ]
    });

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
});