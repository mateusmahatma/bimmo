$(document).ready(function () {
    // Theme Handler
    const skin = window.userSkin || 'auto';
    const updateSkinUrl = window.updateSkinUrl;
    const csrfToken = window.csrfToken;

    function applyTheme(mode) {
        if (mode === 'light' || mode === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', mode);
        } else {
            document.documentElement.removeAttribute('data-bs-theme'); // auto
        }
        document.dispatchEvent(new Event("themeChanged"));
    }

    function highlightActiveSkin(mode) {
        document.querySelectorAll('.dropdown-item').forEach(el => {
            el.classList.remove('active');
            if (el.getAttribute('onclick') === `setTheme('${mode}')`) {
                el.classList.add('active');
            }
        });
    }

    function setTheme(mode) {
        applyTheme(mode);
        highlightActiveSkin(mode);

        fetch(updateSkinUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ skin: mode })
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success) alert("Gagal menyimpan tema.");
            })
            .catch(err => console.error("Gagal update tema:", err));
    }

    // Eksekusi awal tema
    applyTheme(skin);
    highlightActiveSkin(skin);
    window.setTheme = setTheme;

    // Inisialisasi DataTables
    var comparisonTable = $('#comparisonTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        language: {
            processing:
                '<div class="loader-container"><div class="loader"></div></div>',
        }
    });

    var today = moment();
    var start_date = today.startOf('day');
    var end_date = today.endOf('day');

    function setDateRangeText(selector, startDate, endDate) {
        $(selector + ' span').html(startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY'));
    }

    setDateRangeText('#daterange', start_date, end_date);
    $('#daterange').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        applyClass: 'dark-mode',
    }, function (chosen_start_date, chosen_end_date) {
        setDateRangeText('#daterange', chosen_start_date, chosen_end_date);
        $('#start_date_1').val(chosen_start_date.format('YYYY-MM-DD'));
        $('#end_date_1').val(chosen_end_date.format('YYYY-MM-DD'));
    });

    setDateRangeText('#daterange2', start_date, end_date);
    $('#daterange2').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        applyClass: 'dark-mode',
    }, function (chosen_start_date, chosen_end_date) {
        setDateRangeText('#daterange2', chosen_start_date, chosen_end_date);
        $('#start_date_2').val(chosen_start_date.format('YYYY-MM-DD'));
        $('#end_date_2').val(chosen_end_date.format('YYYY-MM-DD'));
    });

    $('#compareForm').on('submit', function (e) {
        e.preventDefault();

        $('.tombol-compare').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');
        $('.tombol-compare').prop('disabled', true);

        let formData = {
            start_date_1: $('#start_date_1').val(),
            end_date_1: $('#end_date_1').val(),
            start_date_2: $('#start_date_2').val(),
            end_date_2: $('#end_date_2').val(),
            pengeluaran: $('#pengeluaran').val(),
            gap: $('#gap').val(),
        };

        $.ajax({
            url: '/compare',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('.tombol-compare').html('Compare');
                $('.tombol-compare').prop('disabled', false);

                // Gunakan DataTables API untuk update data
                comparisonTable.clear().draw();

                response.data.forEach((item) => {
                    comparisonTable.row.add([
                        `<div class="text-center">${item.nominalPeriode1.toLocaleString()}</div>`,
                        `<div class="text-center">${item.nominalPeriode2.toLocaleString()}</div>`,
                        `<div class="text-center" style="color: ${item.color}">${item.gap.toLocaleString()}</div>`
                    ]).draw(false);
                });

                $('#message').text(response.message);
            },
            error: function (xhr) {
                $('.tombol-compare').html('Compare');
                $('.tombol-compare').prop('disabled', false);

                let errors = xhr.responseJSON.errors;
                let errorMessage = '';

                $.each(errors, function (key, value) {
                    errorMessage += value + '\n';
                });

                alert(errorMessage);
            }
        });
    });

    // Clear localStorage values related to form data
    localStorage.removeItem('start_date_1');
    localStorage.removeItem('end_date_1');
    localStorage.removeItem('start_date_2');
    localStorage.removeItem('end_date_2');
    localStorage.removeItem('pengeluaran');
    localStorage.removeItem('selisih');

    // Reset form data to today's date
    $('#start_date_1').val(start_date.format('YYYY-MM-DD'));
    $('#end_date_1').val(end_date.format('YYYY-MM-DD'));
    $('#start_date_2').val(start_date.format('YYYY-MM-DD'));
    $('#end_date_2').val(end_date.format('YYYY-MM-DD'));
    $('#pengeluaran').val('');
    $('#selisih').val('');
});