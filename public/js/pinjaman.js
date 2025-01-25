$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $("#pinjamanTable").DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        language: {
            processing:
                '<div class="loader-container"><div class="loader"></div></div>',
        },
        ajax: {
            url: "/pinjaman",
            type: "GET",
            dataSrc: function (json) {
                return json.data;
            },
            dataSrc: function (json) {
                $("#totalPinjaman").text(
                    json.totalPinjaman.toLocaleString("id-ID")
                );
                return json.data;
            },
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
                data: "nama_pinjaman",
                className: "text-center",
                render: function (data) {
                    return data ? data : "-";
                },
            },
            {
                data: "jumlah_pinjaman",
            },

            {
                data: "status",
                className: "text-center",
                render: function (data) {
                    if (data === "belum_lunas") {
                        return '<span class="badge badge-danger">Belum Lunas</span>';
                    } else if (data === "lunas") {
                        return '<span class="badge badge-success">Lunas</span>';
                    }
                    return data ? data : "-";
                },
            },
            {
                data: "aksi",
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (data, type, row) {
                    return data;
                },
            },
        ],
    });

    $(document).ready(function () {
        $("#bayarModal").on("show.bs.modal", function (event) {
            var button = $(event.relatedTarget);
            var pinjamanId = button.data("pinjaman-id");
            var modal = $(this);
            var form = modal.find("#bayarForm");
            form.attr("action", "/pinjaman/" + pinjamanId + "/bayar");
            modal.find("#pinjamanId").val(pinjamanId);
        });
    });

    // Proses Delete
    $("body").on("click", ".tombol-del-pinjaman", function (e) {
        e.preventDefault();

        var id = $(this).data("id");

        var toastMixin = Swal.mixin({
            toast: true,
            icon: "success",
            title: "General Title",
            animation: false,
            position: "top",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: false,
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        });

        // SweetAlert2 for confirmation
        Swal.fire({
            title: "Yakin mau hapus data ini?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/pinjaman/" + id,
                    type: "DELETE",
                    success: function (response) {
                        toastMixin.fire({
                            animation: true,
                            title: response.message,
                            customClass: {
                                title: "swal2-title-create",
                                popup: "swal2-popup-create",
                            },
                            iconColor: "#ffffff",
                        });
                        $("#pinjamanTable").DataTable().ajax.reload();
                    },
                    error: function (xhr) {
                        var errorMsg =
                            xhr.responseJSON?.message || "Data Gagal dihapus";
                        $("#toastTransaksi").text(errorMsg);
                        $(".toast").addClass("bg-danger");
                        $(".toast").toast("show");
                        $("#pinjamanTable").DataTable().ajax.reload();
                    },
                });
            }
        });
    });

    // Proses Update
    $("body").on("click", ".tombol-edit-pinjaman", function (e) {
        e.preventDefault();

        var id = $(this).data("id");

        $.ajax({
            url: "/pinjaman/" + id,
            type: "GET",
            success: function (response) {
                $("#editPinjamanModal" + id + " #nama_pinjaman").val(
                    response.pinjaman.nama_pinjaman
                );
                $("#editPinjamanModal" + id + " #jumlah_pinjaman").val(
                    response.pinjaman.jumlah_pinjaman
                );
                $("#editPinjamanModal" + id).modal("show");
            },
            error: function (xhr) {
                var errorMsg =
                    xhr.responseJSON?.message || "Data gagal diambil";
                $("#toastTransaksi").text(errorMsg);
                $(".toast").addClass("bg-danger");
                $(".toast").toast("show");
            },
        });
    });

    // Proses menyimpan perubahan update
    $("body").on("submit", ".form-update-pinjaman", function (e) {
        e.preventDefault();

        var form = $(this);
        var id = form.data("id");

        $.ajax({
            url: "/pinjaman/" + id,
            type: "PUT",
            data: form.serialize(),
            success: function (response) {
                Swal.fire({
                    title: "Berhasil!",
                    text: response.message,
                    icon: "success",
                });
                $("#pinjamanTable").DataTable().ajax.reload();
                $("#editPinjamanModal" + id).modal("hide");
            },
            error: function (xhr) {
                var errorMsg =
                    xhr.responseJSON?.message || "Data gagal diperbarui";
                $("#toastTransaksi").text(errorMsg);
                $(".toast").addClass("bg-danger");
                $(".toast").toast("show");
            },
        });
    });
});
