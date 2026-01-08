// TinyMCE Initialization
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
            images_upload_url: window.uploadImageRoute || '/admin/upload-image',
            automatic_uploads: true,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', window.uploadImageRoute || '/admin/upload-image');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content || '');
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
            uploadcare_public_key: 'uljukhturdir8kfxmaz6fjy232zpyv0yp476vwk2havbs81j',
        });
    }
});
