// Common Actions: Upload Preview & Delete Item
document.addEventListener('DOMContentLoaded', function() {
    // Upload Preview
    if (typeof $.uploadPreview === 'function') {
        $.uploadPreview({
            input_field: "#image-upload",
            preview_box: "#image-preview",
            label_field: "#image-label",
            label_default: "Choose File",
            label_selected: "Change File",
            no_label: false,
            success_callback: null
        });
    }

    // Delete Item with SweetAlert confirmation
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
                    data: {_token: document.querySelector('meta[name="csrf-token"]')?.content || ''},
                    success: function(response) {
                        if(response.status === 'success'){
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                            window.location.reload();
                        }else if (response.status === 'error'){
                            Swal.fire(
                                'Something went wrong!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error){
                        console.log(error);
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the item.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
