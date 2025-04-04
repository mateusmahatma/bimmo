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
                console.log("Response dari server:", json);
                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'tgl_transaksi_dana_darurat', className: 'text-center', render: data => moment(data).format('YYYY-MM-DD') },
            { data: 'jenis_transaksi_dana_darurat', className: 'text-center', render: data => parseFloat(data).toLocaleString("id-ID") },
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