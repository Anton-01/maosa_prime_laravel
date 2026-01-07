/**
 * TinyMCE Initialization with Image Upload Support
 * This file initializes TinyMCE editor with full features including image upload
 */

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    initializeTinyMCE();
});

// Also export for dynamic use
window.initializeTinyMCE = initializeTinyMCE;

function initializeTinyMCE() {
    // Check if TinyMCE is loaded
    if (typeof tinymce === 'undefined') {
        console.error('TinyMCE is not loaded. Please include TinyMCE script before this file.');
        return;
    }

    // Get CSRF token for Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Initialize TinyMCE on all elements with class 'tinymce' or 'summernote'
    tinymce.init({
        selector: 'textarea.tinymce, textarea.summernote',
        height: 400,
        menubar: true,

        // Plugins for free version
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],

        // Toolbar configuration
        toolbar: 'undo redo | blocks | ' +
            'bold italic forecolor backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | image media link | code fullscreen | help',

        // Content style
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',

        // Image upload configuration
        images_upload_url: '/admin/upload-image',
        automatic_uploads: true,
        images_reuse_filename: false,

        // Image upload handler with fetch API
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                const xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '/admin/upload-image');

                // Set CSRF token header
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };

                xhr.onload = function () {
                    if (xhr.status === 403) {
                        reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                        return;
                    }

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
                    reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };

                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                xhr.send(formData);
            });
        },

        // File picker for more control
        file_picker_types: 'image',
        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype === 'image') {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.onchange = function () {
                    const file = this.files[0];
                    const reader = new FileReader();

                    reader.onload = function () {
                        const id = 'blobid' + (new Date()).getTime();
                        const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        const base64 = reader.result.split(',')[1];
                        const blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);

                        // Upload the image
                        const formData = new FormData();
                        formData.append('file', file);

                        fetch('/admin/upload-image', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        })
                            .then(response => response.json())
                            .then(result => {
                                callback(result.location, { alt: file.name });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                // Fallback to base64 if upload fails
                                callback(reader.result, { alt: file.name });
                            });
                    };

                    reader.readAsDataURL(file);
                };

                input.click();
            }
        },

        // Additional settings
        branding: false,
        promotion: false,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,

        // Language
        language: 'es',
        language_url: '/admin/assets/js/tinymce/langs/es.js',

        // Setup callback for additional customization
        setup: function (editor) {
            editor.on('init', function () {
                console.log('TinyMCE initialized successfully');
            });
        }
    });
}

// Function to destroy TinyMCE instances (useful for dynamic content)
window.destroyTinyMCE = function(selector) {
    if (typeof tinymce !== 'undefined') {
        tinymce.remove(selector);
    }
};

// Function to get content from TinyMCE editor
window.getTinyMCEContent = function(selector) {
    if (typeof tinymce !== 'undefined') {
        const editor = tinymce.get(selector);
        return editor ? editor.getContent() : '';
    }
    return '';
};
