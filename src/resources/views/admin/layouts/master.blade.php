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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <link rel="stylesheet" href="{{ asset('admin/assets/modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">


    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/components.css') }}">

    @filamentStyles
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('admin/assets/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script src="{{ asset('admin/assets/modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/00000/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

    @filamentScripts
    @livewireScripts

    <!-- Template JS File -->
    <script src="{{ asset('admin/assets/js/scripts.js') }}"></script>

    <!-- TinyMCE Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: 'textarea.tinymce, textarea.summernote',
                    height: 400,
                    menubar: true,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | bold italic forecolor backcolor | ' +
                        'alignleft aligncenter alignright alignjustify | ' +
                        'bullist numlist outdent indent | removeformat | image media link | help',
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                    images_upload_url: '{{ route('admin.upload-image') }}',
                    automatic_uploads: true,
                    images_upload_handler: function (blobInfo, progress) {
                        return new Promise(function (resolve, reject) {
                            const xhr = new XMLHttpRequest();
                            xhr.withCredentials = false;
                            xhr.open('POST', '{{ route('admin.upload-image') }}');
                            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                            xhr.upload.onprogress = function (e) {
                                progress(e.loaded / e.total * 100);
                            };
                            xhr.onload = function () {
                                if (xhr.status < 200 || xhr.status >= 300) {
                                    reject('HTTP Error: ' + xhr.status);
                                    return;
                                }
                                const json = JSON.parse(xhr.responseText);
                                if (!json || typeof json.location != 'string') {
                                    reject('Invalid JSON: ' + xhr.responseText);
                                    return;
                                }
                                resolve(json.location);
                            };
                            xhr.onerror = function () {
                                reject('Image upload failed');
                            };
                            const formData = new FormData();
                            formData.append('file', blobInfo.blob(), blobInfo.filename());
                            xhr.send(formData);
                        });
                    },
                    file_picker_types: 'image',
                    branding: false,
                    promotion: false,
                    relative_urls: false,
                    remove_script_host: false,
                    convert_urls: true,
                    uploadcare_public_key: '00000',
                });
            }
        });
    </script>
    <script>
        @if ($errors->any())
        window.addEventListener('load', function() {
            @foreach ($errors->all() as $error)
            new FilamentNotification()
                .title('Error de validación')
                .body('{{ addslashes($error) }}')
                .danger()
                .send();
            @endforeach
        });
        @endif
        // Display session flash messages using Filament Notifications
        @if (session('success'))
        window.addEventListener('load', function() {
            new FilamentNotification()
                .title('Éxito')
                .body('{{ addslashes(session('success')) }}')
                .success()
                .send();
        });
        @endif
        @if (session('error'))
        window.addEventListener('load', function() {
            new FilamentNotification()
                .title('Error')
                .body('{{ addslashes(session('error')) }}')
                .danger()
                .send();
        });
        @endif
        @if (session('warning'))
        window.addEventListener('load', function() {
            new FilamentNotification()
                .title('Advertencia')
                .body('{{ addslashes(session('warning')) }}')
                .warning()
                .send();
        });
        @endif
        @if (session('info'))
        window.addEventListener('load', function() {
            new FilamentNotification()
                .title('Información')
                .body('{{ addslashes(session('info')) }}')
                .info()
                .send();
        });
        @endif

        $.uploadPreview({
            input_field: "#image-upload",
            preview_box: "#image-preview",
            label_field: "#image-label",
            label_default: "Choose File",
            label_selected: "Change File",
            no_label: false,
            success_callback: null
        });

        $('body').on('click', '.delete-item', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        method: 'DELETE',
                        url: url,
                        data: {_token: "{{ csrf_token() }}"},
                        success: function(response) {
                            if(response.status === 'success'){
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                )
                                window.location.reload();
                            }else if (response.status === 'error'){
                                Swal.fire(
                                    'Something wen\'t wrong!',
                                    response.message,
                                    'error'
                                )
                            }
                        },
                        error: function(xhr, status, error){
                            console.log(error);
                        }

                    })

                }
            })

        })
    </script>
    @stack('scripts')
</body>

</html>
