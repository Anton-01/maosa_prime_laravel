<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>General Dashboard &mdash; Maosa Prime</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('admin/assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap-iconpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/timePicker/jquery.timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">

    <!-- CSS Data Table -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/dataTable/dataTables.bootstrap5.min.css') }}">

    <!-- Template CSS (Custom styles only) -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/components.css') }}">

    @stack('styles')

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            @include('admin.layouts.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                @yield('contents')
            </div>

            <footer class="main-footer">
                <div class="footer-left">
                    Copyright &copy; {{ date('Y') }} <div class="bullet"></div> Design By <a href="https://bullup.com.mx/">BullUp</a>
                </div>
                <div class="footer-right">

                </div>
            </footer>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('admin/assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/popper.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/stisla.js') }}"></script>

    <!-- JS Libraies -->
    <script src="{{ asset('admin/assets/modules/upload-preview/assets/js/jquery.uploadPreview.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/sweetAlert/sweet-alert-v2.js') }}"></script>
    <script src="{{ asset('admin/assets/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/timePicker/jquery.timepicker.min.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>

    <!-- DataTables JS -->
    <script src="{{ asset('admin/assets/js/dataTable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/dataTable/dataTables.bootstrap5.min.js') }}"></script>

    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/uljukhturdir8kfxmaz6fjy232zpyv0yp476vwk2havbs81j/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

    <!-- Template JS File -->
    <script src="{{ asset('admin/assets/js/scripts.js') }}"></script>

    <!-- TinyMCE Initialization -->
    <script>
        window.uploadImageRoute = '{{ route('admin.upload-image') }}';
    </script>

    <script src="{{ asset('admin/assets/js/tinymce-init.js') }}"></script>

    <!-- Flash Messages & Notifications -->
    <script src="{{ asset('admin/assets/js/notifications.js') }}"></script>

    <!-- Upload Preview & Delete Item -->
    <script src="{{ asset('admin/assets/js/common-actions.js') }}"></script>

    @stack('scripts')

    <!-- PHPFlasher Toastr Notifications -->
    @flasher_render
</body>

</html>
