(function () {
    'use strict';

    var isInit = false;
    function initDompetPage() {
        if (isInit) return;
        isInit = true;
        setTimeout(function () {
            isInit = false;
        }, 500);

        if (!$('#dompetTable').length) return;

        if ($.fn.DataTable.isDataTable('#dompetTable')) {
            $('#dompetTable').DataTable().destroy();
        }

        $('#dompetTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            columnDefs: [
                { orderable: false, targets: 2 } // Actions column not orderable
            ],
            order: [[0, 'asc']] // Order by name by default
        });
    }

    $(document).ready(initDompetPage);
    document.addEventListener('livewire:navigated', initDompetPage);
})();
