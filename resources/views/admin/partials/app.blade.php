<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Around Airports - @yield('title')</title>

    <!-- Custom fonts for this template-->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/admin/img/favicon.svg') }}">
    <link href="{{ asset('assets/admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- Custom styles for this template-->
    <link href="{{ mix('assets/admin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/sweetalert/lib/sweetalert.css') }}" />
    @yield('style')
    <style>
        .fas::before{
            font-family: "Font Awesome 5 Free" !important;
        }
        .card-header-actions .card-header {
            height: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 0.5625rem;
            padding-bottom: 0.5625rem;
        }
    </style>
</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

@include('admin.partials.sidebar')

<!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            @include('admin.partials.topbar')

            <div id="app">
            @yield('content')
            </div>

        </div>
        <!-- End of Main Content -->

        @include('admin.partials.footer')

    </div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Bootstrap core JavaScript-->
<script src="{{ asset('assets/admin/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

<!-- Core plugin JavaScript-->
<script src="{{ asset('assets/admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<script src="{{ asset('assets/admin/vendor/sweetalert/lib/sweet-alert.js') }}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('assets/admin/js/sb-admin-2.min.js') }}"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@yield('script')
<script src="{{ mix('assets/admin/js/app.js') }}"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
</body>

</html>
