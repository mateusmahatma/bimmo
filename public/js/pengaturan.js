$(document).ready(function () {
    $('#pengaturanTable').DataTable({
        "paging": true,
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        serverSide: true,
        processing: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>'
        },
        ajax: function (data, callback) {
            $.ajax({
                url: '/pengaturan',
                'data': data,
                dataType: 'json',
                beforeSend: function () {
                    $('#pengaturanTable > tbody').html(
                        '<tr class="odd">' +
                        '<td valign="top" colspan="6" class="dataTables_empty">Loading&hellip;</td>' +
                        '</tr>'
                    );
                },
                success: function (res) {
                    callback(res);
                }
            });
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false,
            className: 'text-center'
        }, {
            data: 'name',
            name: 'Nama',
            className: 'text-center'
        },
        {
            data: 'email',
            name: 'Email',
            className: 'text-center'
        }, {
            data: '',
            name: 'Akses Menu',
            className: 'text-center'
        }, {
            data: 'created_at',
            name: 'Dibuat Tanggal',
            render: function (data) {
                // Gunakan Moment.js untuk memformat timestamp
                return moment(data).format('YYYY-MM-DD HH:mm:ss');
            },
            className: 'text-center'
        }, {
            data: 'updated_at',
            name: 'Diupdate Tanggal',
            render: function (data) {
                // Gunakan Moment.js untuk memformat timestamp
                return moment(data).format('YYYY-MM-DD HH:mm:ss');
            },
            className: 'text-center'
        }, {
            data: 'aksi',
            name: 'aksi'
        }
        ]
    });
});

// Fungsi Simpan & Update
// function simpanPemasukan(id = '') {
//     var var_url, var_type;
//     if (id == '') {
//         var var_url = '/pemasukan/';
//         var var_type = 'POST';
//     } else {
//         var var_url = '/pemasukan/' + id;
//         var var_type = 'PUT';
//     }
//     // Define the AJAX request inside the modal's callback
//     $('#pemasukanModal').on('shown.bs.modal', function () {
//         var toastMixin = Swal.mixin({
//             toast: true,
//             icon: 'success',
//             title: 'General Title',
//             animation: false,
//             position: 'top',
//             showConfirmButton: false,
//             timer: 3000,
//             timerProgressBar: false,
//             didOpen: (toast) => {
//                 toast.addEventListener('mouseenter', Swal.stopTimer)
//                 toast.addEventListener('mouseleave', Swal.resumeTimer)
//             }
//         });
//         $('#pemasukanModal').off('click', '.tombol-simpan-pemasukan'); // Hapus event listener sebelumnya
//         // Handle the confirmation button click inside the modal
//         $('#pemasukanModal').on('click', '.tombol-simpan-pemasukan', function () {
//             // Disable the login button to prevent multiple clicks
//             $('.tombol-simpan-pemasukan').prop('disabled', true);
//             // Library Spinner
//             $('.tombol-simpan-pemasukan').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');
//             // Get the data from the login form
//             // Get the data from the modal, replace 'yourData' with the actual way you retrieve the data
//             var formData = {
//                 nama: $('#nama').val(),
//             };
//             var namaValue = $('#nama').val();
//             // Check if the value is not empty
//             if (namaValue.trim() !== '') {
//                 // Add to formData if not empty
//                 formData.nama = namaValue;
//             } else {
//                 // Enable the login button in case of an error
//                 $('.tombol-simpan-pemasukan').prop('disabled', false);
//                 // Restore the original text of the login button
//                 $('.tombol-simpan-pemasukan').html('Simpan');
//                 // Stop the loading animation
//                 $('.tombol-simpan-pemasukan .spinner-border').remove();
//                 // Display error toast if login fails
//                 // Handle the case where the field is required, and the value is empty
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Oops...',
//                     text: 'Nama Harus Diisi!',
//                 }); // You can replace this with your preferred way of handling empty required fields
//             }
//             // Perform the AJAX request
//             $.ajax({
//                 url: var_url,
//                 type: var_type,
//                 data: formData,
//                 success: function () {
//                     // Display success toast
//                     toastMixin.fire({
//                         animation: true,
//                         title: 'Data Berhasil disimpan'
//                     });
//                     $('#pemasukanModal').modal('hide'); // Hide the modal
//                     $('#pemasukanTable').DataTable().ajax.reload();
//                 },
//                 complete: function () {
//                     // Aktifkan kembali tombol setelah permintaan selesai (baik sukses maupun gagal)
//                     $('.tombol-simpan-pemasukan').prop('disabled', false);
//                     $('.tombol-simpan-pemasukan').html('Simpan');
//                     $('.tombol-simpan-pemasukan .spinner-border').remove();
//                 },
//             });
//         });
//         // Menghilangkan value di form setelah simpan
//         $('#pemasukanModal').on('hidden.bs.modal', function () {
//             $('#nama').val('');
//             $('.tombol-simpan-pemasukan').html('Simpan');
//             $('.tombol-simpan-pemasukan .spinner-border').remove();
//         });
//     });
// };

// // Global Setup
// $.ajaxSetup({
//     headers: {
//         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//     }
// });

// // Proses Simpan
// $('body').on('click', '.tombol-tambah-pemasukan', function (e) {
//     e.preventDefault();
//     // Define the AJAX request inside the modal's callback
//     $('#pemasukanModal').on('shown.bs.modal', function () {
//         var toastMixin = Swal.mixin({
//             toast: true,
//             icon: 'success',
//             title: 'General Title',
//             animation: false,
//             position: 'top',
//             showConfirmButton: false,
//             timer: 4000,
//             timerProgressBar: false,
//             didOpen: (toast) => {
//                 toast.addEventListener('mouseenter', Swal.stopTimer)
//                 toast.addEventListener('mouseleave', Swal.resumeTimer)
//             }
//         });

//         // Handle the confirmation button click inside the modal
//         $('#pemasukanModal').on('click', '.tombol-simpan-pemasukan', function () {
//             // Disable the login button to prevent multiple clicks
//             $('.tombol-simpan-pemasukan').prop('disabled', true);
//             // Library Spinner
//             $('.tombol-simpan-pemasukan').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');
//             // Get the data from the login form
//             // Get the data from the modal, replace 'yourData' with the actual way you retrieve the data
//             var formData = {
//                 nama: $('#nama').val(),
//             };
//             var namaValue = $('#nama').val();
//             // Check if the value is not empty
//             if (namaValue.trim() !== '') {
//                 // Add to formData if not empty
//                 formData.nama = namaValue;
//             } else {
//                 // Enable the login button in case of an error
//                 $('.tombol-simpan-pemasukan').prop('disabled', false);
//                 // Restore the original text of the login button
//                 $('.tombol-simpan-pemasukan').html('Simpan');
//                 // Stop the loading animation
//                 $('.tombol-simpan-pemasukan .spinner-border').remove();
//                 // Display error toast if login fails
//                 // Handle the case where the field is required, and the value is empty
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Oops...',
//                     text: 'Nama Harus Diisi!',
//                 }); // You can replace this with your preferred way of handling empty required fields
//             }
//             // Perform the AJAX request
//             $.ajax({
//                 url: '/pemasukan/',
//                 type: 'post',
//                 data: formData,
//                 success: function () {
//                     // Display success toast
//                     toastMixin.fire({
//                         animation: true,
//                         title: 'Data Berhasil disimpan'
//                     });
//                     $('#pemasukanModal').modal('hide'); // Hide the modal
//                     $('#pemasukanTable').DataTable().ajax.reload();
//                 },
//                 complete: function () {
//                     // Aktifkan kembali tombol setelah permintaan selesai (baik sukses maupun gagal)
//                     $('.tombol-simpan-pemasukan').prop('disabled', false);
//                     $('.tombol-simpan-pemasukan').html('Simpan');
//                     $('.tombol-simpan-pemasukan .spinner-border').remove();
//                 },
//             });
//         });
//         // Menghilangkan value di form setelah simpan
//         $('#pemasukanModal').on('hidden.bs.modal', function () {
//             $('#nama').val('');
//             $('.tombol-simpan-pemasukan').html('Simpan');
//             $('.tombol-simpan-pemasukan .spinner-border').remove();
//         });
//         simpanPemasukan();
//     });
// });


// // Proses Edit
// $('body').on('click', '.tombol-edit-pemasukan', function (e) {
//     var id = $(this).data('id');

//     $.ajax({
//         url: 'pemasukan/' + id + '/edit',
//         type: 'GET',
//         success: function (response) {
//             $('#pemasukanModal').modal('show');
//             $('#nama').val(response.result.nama);
//             console.log(response.result);
//             simpanPemasukan(id);
//         }
//     });
// });

// // Proses Delete
// $('body').on('click', '.tombol-del-pemasukan', function (e) {
//     e.preventDefault(); // Prevent the default behavior of the link
//     // toastMixin
//     var toastMixin = Swal.mixin({
//         toast: true,
//         icon: 'success',
//         title: 'General Title',
//         animation: false,
//         position: 'top',
//         showConfirmButton: false,
//         timer: 4000,
//         timerProgressBar: false,
//         didOpen: (toast) => {
//             toast.addEventListener('mouseenter', Swal.stopTimer)
//             toast.addEventListener('mouseleave', Swal.resumeTimer)
//         }
//     });
//     // SweetAlert Confirm Delete
//     Swal.fire({
//         title: 'Yakin mau hapus data ini?',
//         html: `Data yang dihapus tidak dapat dikembalikan!`,
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#d33',
//         cancelButtonColor: '#3085d6',
//         confirmButtonText: 'Ya, hapus!',
//         cancelButtonText: 'Batal'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             var id = $(this).data('id');
//             $.ajax({
//                 url: '/pemasukan/' + id,
//                 type: 'DELETE',
//                 success: function () {
//                     // Display success toast
//                     toastMixin.fire({
//                         animation: true,
//                         title: 'Data Berhasil dihapus'
//                     });
//                     $('#pemasukanTable').DataTable().ajax.reload();
//                 },
//                 error: function () {
//                     // Display error toast
//                     $('#toastPemasukan').text('Data Gagal dihapus');
//                     $('.toast').addClass('bg-danger');
//                     $('.toast').toast('show');
//                     $('#pemasukanTable').DataTable().ajax.reload();
//                 }
//             });
//         }
//     });
// });