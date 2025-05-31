<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link rel="icon" href="{{ asset('img/bimmo_icon.png') }}" />

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/boxicons/css/boxicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/quill/quill.snow.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/quill/quill.bubble.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/remixicon/remixicon.css') }}" />

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tom-select.bootstrap5.min.css') }}" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}" />
    <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" />

</head>

<body>
    <!-- Header -->
    @include('layouts.header')

    <!-- Main -->
    <main id="main" class="main">
        @yield('container')
    </main>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="/vendor/apexcharts/apexcharts.min.js" async></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/vendor/chart.js/chart.umd.js"></script>
    <script src="/vendor/echarts/echarts.min.js"></script>
    <script src="/vendor/quill/quill.min.js"></script>
    <script src="/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/vendor/tinymce/tinymce.min.js"></script>

    <!-- DataTables  & Plugins -->
    <script src="/js/jquery-3.7.0.min.js"></script>
    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="/plugins/jszip/jszip.min.js"></script>
    <script src="/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('js/main.js') }}?v={{ filemtime(public_path('js/main.js')) }}"></script>


    @yield('scripts')

    <!-- Vendor JS -->
    <script src="{{ asset('js/vendor/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/vendor/moment.min.js') }}"></script>
    <script src="{{ asset('js/vendor/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('js/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/vendor/tom-select.complete.min.js') }}"></script>

</body>

</html>